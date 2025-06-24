@extends('layouts.app')

@section('title', '¡Felicitaciones! - MediCare Pro')

@section('content')
<div class="onboarding-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                
                <!-- Success Animation -->
                <div class="success-animation text-center mb-4">
                    <div class="success-checkmark">
                        <div class="check-icon">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>

                <!-- Congratulations Card -->
                <div class="congratulations-card text-center">
                    <div class="card-header">
                        <h1 class="congratulations-title">¡Felicitaciones!</h1>
                        <p class="congratulations-subtitle">Tu cuenta está completamente configurada</p>
                    </div>
                    
                    <div class="card-body">
                        <div class="completion-message mb-4">
                            <h4>🎉 ¡Todo listo para comenzar!</h4>
                            <p>Has completado exitosamente la configuración inicial. Tu cuenta de MediCare Pro está lista para usar.</p>
                        </div>
                        
                        <!-- Next Steps -->
                        <div class="next-steps">
                            <h4 class="section-title">
                                <i class="fas fa-rocket"></i>
                                ¿Qué puedes hacer ahora?
                            </h4>
                            
                            <div class="next-steps-grid">
                                <div class="next-step-item">
                                    <div class="step-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <h5>Gestionar Citas</h5>
                                    <p>Programa y administra citas con tus pacientes</p>
                                </div>
                                
                                <div class="next-step-item">
                                    <div class="step-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h5>Ver Pacientes</h5>
                                    <p>Accede al historial médico completo</p>
                                </div>
                                
                                <div class="next-step-item">
                                    <div class="step-icon">
                                        <i class="fas fa-video"></i>
                                    </div>
                                    <h5>Videollamadas</h5>
                                    <p>Consultas virtuales instantáneas</p>
                                </div>
                                
                                <div class="next-step-item">
                                    <div class="step-icon">
                                        <i class="fas fa-share-alt"></i>
                                    </div>
                                    <h5>Compartir Enlace</h5>
                                    <p>Comparte tu enlace de reservas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="card-footer">
                        <form method="POST" action="{{ route('onboarding.finish') }}" class="finish-form">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-play-circle"></i>
                                ¡Comenzar a usar MediCare Pro!
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<style>
.onboarding-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 0;
}

.success-animation {
    margin-bottom: 2rem;
}

.success-checkmark {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #28a745;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: scale 0.5s ease-in-out;
}

.check-icon {
    color: white;
    font-size: 2rem;
}

@keyframes scale {
    0% {
        transform: scale(0);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}

.congratulations-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    animation: slideInUp 0.6s ease-out;
}

@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 2rem;
}

.congratulations-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.congratulations-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
}

.card-body {
    padding: 2rem;
}

.completion-message h4 {
    color: #28a745;
    font-weight: 600;
    margin-bottom: 1rem;
}

.completion-message p {
    color: #6c757d;
    font-size: 1.1rem;
    line-height: 1.6;
}

.section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-align: center;
}

.section-title i {
    margin-right: 0.5rem;
    color: #007bff;
}

.next-steps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.next-step-item {
    text-align: center;
    padding: 1.5rem;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.next-step-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border-color: #007bff;
}

.next-step-item .step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin: 0 auto 1rem;
}

.next-step-item h5 {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #212529;
}

.next-step-item p {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
}

.card-footer {
    background: #f8f9fa;
    padding: 2rem;
    text-align: center;
    border-top: 1px solid #e9ecef;
}

.finish-form .btn {
    padding: 1rem 2rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
    transition: all 0.3s ease;
}

.finish-form .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
}

@media (max-width: 768px) {
    .congratulations-title {
        font-size: 2rem;
    }
    
    .next-steps-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
@endsection 
</script>
@endsection 