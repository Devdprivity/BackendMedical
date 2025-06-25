<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\VideoCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VideoCallController extends Controller
{
    /**
     * Create a new video call for an appointment
     */
    public function create(Request $request, Appointment $appointment)
    {
        // Check if user can create video call for this appointment
        if (!$this->canManageVideoCall($appointment)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Check if appointment already has a video call
        if ($appointment->videoCall) {
            return response()->json([
                'success' => true,
                'video_call' => $appointment->videoCall,
                'message' => 'La videollamada ya existe'
            ]);
        }

        // Generate unique room name and URL
        $roomName = VideoCall::generateRoomName($appointment->id);
        $roomUrl = VideoCall::generateJitsiUrl($roomName);

        // Create video call record
        $videoCall = VideoCall::create([
            'appointment_id' => $appointment->id,
            'room_name' => $roomName,
            'room_url' => $roomUrl,
            'status' => 'pending',
            'recording_enabled' => $request->boolean('recording_enabled', false)
        ]);

        Log::info('Video call created', [
            'appointment_id' => $appointment->id,
            'room_name' => $roomName,
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'video_call' => $videoCall,
            'message' => 'Videollamada creada exitosamente'
        ]);
    }

    /**
     * Start a video call
     */
    public function start(VideoCall $videoCall)
    {
        if (!$this->canManageVideoCall($videoCall->appointment)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        if (!$videoCall->canStart()) {
            return response()->json([
                'error' => 'La videollamada no puede ser iniciada',
                'status' => $videoCall->status
            ], 400);
        }

        $videoCall->start();

        // Add current user as participant
        $videoCall->addParticipant([
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'type' => 'host'
        ]);

        Log::info('Video call started', [
            'video_call_id' => $videoCall->id,
            'appointment_id' => $videoCall->appointment_id,
            'started_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'video_call' => $videoCall->fresh(),
            'message' => 'Videollamada iniciada'
        ]);
    }

    /**
     * Join a video call
     */
    public function join(Request $request, VideoCall $videoCall)
    {
        // For instant video calls
        if ($videoCall->is_instant) {
            // Add user as participant if authenticated
            if (Auth::check()) {
                $videoCall->addParticipant([
                    'user_id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'role' => Auth::user()->role ?? 'guest',
                    'type' => 'participant'
                ]);
            }

            Log::info('User joined instant video call', [
                'video_call_id' => $videoCall->id,
                'user_id' => Auth::id(),
                'is_instant' => true
            ]);

            return response()->json([
                'success' => true,
                'video_call' => $videoCall->fresh(),
                'join_url' => $videoCall->room_url,
                'message' => 'Uniéndose a la videollamada instantánea'
            ]);
        }

        // For appointment-based video calls (original logic)
        $appointment = $videoCall->appointment;
        
        // Check if user can join this video call
        if (!$this->canJoinVideoCall($appointment)) {
            return response()->json(['error' => 'No autorizado para unirse a esta videollamada'], 403);
        }

        // Check if appointment allows video call at this time
        if (!$appointment->canStartVideoCall()) {
            return response()->json([
                'error' => 'La videollamada no está disponible en este momento',
                'message' => 'Solo puedes unirte 15 minutos antes y hasta 60 minutos después de la hora programada'
            ], 400);
        }

        // Add user as participant if not already added
        $videoCall->addParticipant([
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'type' => 'participant'
        ]);

        Log::info('User joined video call', [
            'video_call_id' => $videoCall->id,
            'user_id' => Auth::id(),
            'appointment_id' => $appointment->id
        ]);

        return response()->json([
            'success' => true,
            'video_call' => $videoCall->fresh(),
            'join_url' => $videoCall->room_url,
            'message' => 'Uniéndose a la videollamada'
        ]);
    }

    /**
     * End a video call
     */
    public function end(VideoCall $videoCall)
    {
        // For instant video calls, check different permissions
        if ($videoCall->is_instant) {
            $user = Auth::user();
            
            // Only the creator, admins, or doctors can end instant calls
            if (!($user->role === 'admin' || 
                  $user->role === 'doctor' || 
                  $videoCall->created_by === $user->id)) {
                return response()->json(['error' => 'No autorizado para finalizar esta sala'], 403);
            }
        } else {
            // For appointment-based calls, use original logic
            if (!$this->canManageVideoCall($videoCall->appointment)) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
        }

        $videoCall->end();

        Log::info('Video call ended', [
            'video_call_id' => $videoCall->id,
            'duration' => $videoCall->duration_minutes,
            'ended_by' => Auth::id(),
            'is_instant' => $videoCall->is_instant
        ]);

        return response()->json([
            'success' => true,
            'video_call' => $videoCall->fresh(),
            'message' => $videoCall->is_instant ? 'Sala finalizada' : 'Videollamada finalizada'
        ]);
    }

    /**
     * Show video call room
     */
    public function show(VideoCall $videoCall)
    {
        // For instant video calls (no appointment)
        if ($videoCall->is_instant) {
            // Anyone can join instant video calls with the link
            return view('video-calls.room', [
                'videoCall' => $videoCall,
                'appointment' => null, // No appointment for instant calls
                'user' => Auth::user()
            ]);
        }
        
        // For appointment-based video calls
        $appointment = $videoCall->appointment;
        
        if (!$this->canJoinVideoCall($appointment)) {
            abort(403, 'No autorizado para acceder a esta videollamada');
        }

        return view('video-calls.room', [
            'videoCall' => $videoCall,
            'appointment' => $appointment,
            'user' => Auth::user()
        ]);
    }

    /**
     * Get video call status
     */
    public function status(VideoCall $videoCall)
    {
        $appointment = $videoCall->appointment;
        
        if (!$this->canJoinVideoCall($appointment)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        return response()->json([
            'success' => true,
            'video_call' => $videoCall,
            'appointment' => $appointment,
            'can_start' => $appointment->canStartVideoCall(),
            'participants_count' => count($videoCall->participants ?? [])
        ]);
    }

    /**
     * Check if user can manage video call (create, start, end)
     */
    private function canManageVideoCall(Appointment $appointment): bool
    {
        $user = Auth::user();
        
        // Admins can manage any video call
        if ($user->role === 'admin') {
            return true;
        }
        
        // Doctors can manage their own appointments
        if ($user->role === 'doctor' && $appointment->doctor_id === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Check if user can join video call (patient, doctor, or admin for the appointment)
     */
    private function canJoinVideoCall(Appointment $appointment): bool
    {
        $user = Auth::user();
        
        // Admin can join any video call
        if ($user->role === 'admin') {
            return true;
        }
        
        // Doctor can join their own appointments
        if ($user->role === 'doctor' && $appointment->doctor_id === $user->id) {
            return true;
        }
        
        // Patient can join their own appointments
        if ($user->role === 'patient' && $appointment->patient_id === $user->id) {
            return true;
        }
        
        return false;
    }

    /**
     * Create instant video call room without appointment
     */
    public function createInstant(Request $request)
    {
        // Only doctors and admins can create instant video calls
        if (!in_array(Auth::user()->role, ['doctor', 'admin'])) {
            return response()->json(['error' => 'No autorizado para crear salas instantáneas'], 403);
        }

        // Generate unique room name and URL for instant call
        $roomName = 'instant-' . Auth::id() . '-' . time() . '-' . substr(md5(uniqid()), 0, 8);
        $roomUrl = VideoCall::generateJitsiUrl($roomName);

        // Create video call record without appointment
        $videoCall = VideoCall::create([
            'appointment_id' => null, // No appointment associated
            'room_name' => $roomName,
            'room_url' => $roomUrl,
            'status' => 'active', // Instant calls start as active
            'recording_enabled' => false,
            'started_at' => now(),
            'created_by' => Auth::id(),
            'is_instant' => true,
            'notes' => 'Sala instantánea creada por ' . Auth::user()->name
        ]);

        // Add creator as participant
        $videoCall->addParticipant([
            'user_id' => Auth::id(),
            'name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'type' => 'host'
        ]);

        Log::info('Instant video call created', [
            'video_call_id' => $videoCall->id,
            'room_name' => $roomName,
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'success' => true,
            'video_call' => $videoCall,
            'message' => 'Sala de videollamada instantánea creada exitosamente'
        ]);
    }
}
