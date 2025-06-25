@props(['size' => 'md', 'variant' => 'primary', 'text' => 'Crear Link de Pago'])

@php
$sizeClasses = [
    'sm' => 'btn-sm',
    'md' => '',
    'lg' => 'btn-lg'
];

$variantClasses = [
    'primary' => 'btn-primary',
    'secondary' => 'btn-secondary',
    'success' => 'btn-success',
    'outline-primary' => 'btn-outline-primary'
];

$btnClass = 'btn ' . ($variantClasses[$variant] ?? 'btn-primary') . ' ' . ($sizeClasses[$size] ?? '');
@endphp

<button type="button" class="{{ $btnClass }}" onclick="openCreateModal()" {{ $attributes }}>
    <i class="fas fa-plus me-2"></i>
    {{ $text }}
</button> 