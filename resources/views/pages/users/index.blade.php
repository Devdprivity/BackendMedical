@extends('layouts.app')

@section('title', 'Administración de Usuarios - DrOrganiza')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 class="page-title">Administración de Usuarios</h1>
        <p class="page-subtitle">Gestión de usuarios, roles y permisos del sistema</p>
    </div>
    <div style="display: flex; gap: 1rem;">
        <button class="btn btn-secondary" onclick="showRolesModal()">
            <i class="fas fa-shield-alt"></i>
            Ver Roles y Permisos
        </button>
        <button class="btn btn-primary" onclick="showCreateUserModal()">
            <i class="fas fa-user-plus"></i>
            Nuevo Usuario
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Total Usuarios</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="totalUsers">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--success); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Usuarios Activos</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="activeUsers">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--warning); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Conexiones Recientes</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="recentLogins">-</div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 50px; height: 50px; background: var(--danger); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 18px;">
                <i class="fas fa-user-slash"></i>
            </div>
            <div>
                <div style="font-size: 0.875rem; color: var(--gray-500); margin-bottom: 4px;">Usuarios Suspendidos</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--dark);" id="suspendedUsers">-</div>
            </div>
        </div>
    </div>
</div>

<!-- Role Distribution Chart -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Distribución de Roles</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1.5rem;">
            <div class="role-stat" data-role="admin">
                <div class="role-icon" style="background: var(--danger);">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="role-info">
                    <div class="role-name">Administradores</div>
                    <div class="role-count" id="adminCount">-</div>
                </div>
            </div>
            <div class="role-stat" data-role="doctor">
                <div class="role-icon" style="background: var(--primary);">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="role-info">
                    <div class="role-name">Doctores</div>
                    <div class="role-count" id="doctorCount">-</div>
                </div>
            </div>
            <div class="role-stat" data-role="nurse">
                <div class="role-icon" style="background: var(--success);">
                    <i class="fas fa-user-nurse"></i>
                </div>
                <div class="role-info">
                    <div class="role-name">Enfermeras</div>
                    <div class="role-count" id="nurseCount">-</div>
                </div>
            </div>
            <div class="role-stat" data-role="receptionist">
                <div class="role-icon" style="background: var(--secondary);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="role-info">
                    <div class="role-name">Recepcionistas</div>
                    <div class="role-count" id="receptionistCount">-</div>
                </div>
            </div>
            <div class="role-stat" data-role="accountant">
                <div class="role-icon" style="background: var(--warning);">
                    <i class="fas fa-calculator"></i>
                </div>
                <div class="role-info">
                    <div class="role-name">Contadores</div>
                    <div class="role-count" id="accountantCount">-</div>
                </div>
            </div>
            <div class="role-stat" data-role="lab_technician">
                <div class="role-icon" style="background: var(--accent);">
                    <i class="fas fa-microscope"></i>
                </div>
                <div class="role-info">
                    <div class="role-name">Técnicos Lab</div>
                    <div class="role-count" id="labTechnicianCount">-</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-body">
        <div style="display: grid; grid-template-columns: 1fr auto auto auto auto; gap: 1rem; align-items: end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Buscar usuarios</label>
                <div style="position: relative;">
                    <input type="text" class="form-control" placeholder="Buscar por nombre o email..." id="searchInput">
                    <i class="fas fa-search" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-400);"></i>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Rol</label>
                <select class="form-control" id="roleFilter">
                    <option value="">Todos los roles</option>
                    <option value="admin">Administrador</option>
                    <option value="doctor">Doctor</option>
                    <option value="nurse">Enfermera</option>
                    <option value="receptionist">Recepcionista</option>
                    <option value="accountant">Contador</option>
                    <option value="lab_technician">Técnico de Laboratorio</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Estado</label>
                <select class="form-control" id="statusFilter">
                    <option value="">Todos los estados</option>
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                    <option value="suspended">Suspendido</option>
                </select>
            </div>
            
            <button class="btn btn-outline" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
            
            <button class="btn btn-secondary" onclick="exportUsers()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="card-title">Lista de Usuarios</h3>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span style="font-size: 0.875rem; color: var(--gray-500);">Mostrando</span>
            <select class="form-control" style="width: auto; min-width: 80px;" id="perPageSelect">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
            <span style="font-size: 0.875rem; color: var(--gray-500);">por página</span>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div id="usersTableContainer">
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                <p>Cargando usuarios...</p>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<div id="paginationContainer" style="margin-top: 2rem;"></div>

<!-- Create User Modal -->
<div id="createUserModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Crear Nuevo Usuario</h3>
            <button class="modal-close" onclick="closeCreateUserModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="createUserForm">
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="password" required minlength="8">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <select class="form-control" name="role" required>
                        <option value="">Seleccionar rol...</option>
                        <option value="admin">Administrador</option>
                        <option value="doctor">Doctor</option>
                        <option value="nurse">Enfermera</option>
                        <option value="receptionist">Recepcionista</option>
                        <option value="accountant">Contador</option>
                        <option value="lab_technician">Técnico de Laboratorio</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select class="form-control" name="status">
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" onclick="closeCreateUserModal()" style="flex: 1;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-user-plus"></i>
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Roles and Permissions Modal -->
<div id="rolesModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h3>Roles y Permisos del Sistema</h3>
            <button class="modal-close" onclick="closeRolesModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div id="rolesContainer">
                <div style="text-align: center; padding: 2rem;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                    <p>Cargando roles y permisos...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Editar Usuario</h3>
            <button class="modal-close" onclick="closeEditUserModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                <input type="hidden" name="user_id">
                
                <div class="form-group">
                    <label class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nueva Contraseña (opcional)</label>
                    <input type="password" class="form-control" name="password" minlength="8">
                    <small style="color: var(--gray-600);">Dejar en blanco para mantener la contraseña actual</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Rol</label>
                    <select class="form-control" name="role" required>
                        <option value="admin">Administrador</option>
                        <option value="doctor">Doctor</option>
                        <option value="nurse">Enfermera</option>
                        <option value="receptionist">Recepcionista</option>
                        <option value="accountant">Contador</option>
                        <option value="lab_technician">Técnico de Laboratorio</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select class="form-control" name="status" required>
                        <option value="active">Activo</option>
                        <option value="inactive">Inactivo</option>
                        <option value="suspended">Suspendido</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline" onclick="closeEditUserModal()" style="flex: 1;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="fas fa-save"></i>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
    vertical-align: middle;
}

.users-table th {
    font-weight: 600;
    color: var(--dark);
    background: var(--gray-100);
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.users-table tr:hover {
    background: var(--gray-100);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: white;
    font-size: 16px;
}

.user-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--dark);
}

.user-details p {
    margin: 0;
    font-size: 0.875rem;
    color: var(--gray-500);
}

.role-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.role-admin {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.role-doctor {
    background: rgba(0, 83, 155, 0.1);
    color: var(--primary);
}

.role-nurse {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.role-receptionist {
    background: rgba(0, 174, 239, 0.1);
    color: var(--secondary);
}

.role-accountant {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.role-lab_technician {
    background: rgba(139, 92, 246, 0.1);
    color: var(--accent);
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.status-inactive {
    background: rgba(107, 114, 128, 0.1);
    color: var(--gray-600);
}

.status-suspended {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
}

.role-stat {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--gray-50);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.role-stat:hover {
    background: var(--gray-100);
    transform: translateY(-2px);
}

.role-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.role-info {
    flex: 1;
}

.role-name {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.25rem;
}

.role-count {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--dark);
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--dark);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--gray-500);
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    color: var(--gray-700);
}

.modal-body {
    padding: 2rem;
}

.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
    margin-top: 1rem;
}

.permission-item {
    padding: 0.5rem 0.75rem;
    background: var(--gray-100);
    border-radius: 6px;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.role-card {
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.role-card h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--dark);
}

.role-card p {
    margin: 0 0 1rem 0;
    color: var(--gray-600);
}

@media (max-width: 768px) {
    .users-table,
    .users-table tbody,
    .users-table tr,
    .users-table td {
        display: block;
    }
    
    .users-table thead {
        display: none;
    }
    
    .users-table tr {
        margin-bottom: 1rem;
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 1rem;
    }
    
    .users-table td {
        border: none;
        padding: 0.5rem 0;
        display: flex;
        justify-content: space-between;
    }
    
    .users-table td:before {
        content: attr(data-label);
        font-weight: 600;
        color: var(--dark);
    }
}
</style>
@endpush

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {};

document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    loadUsersStats();
    
    // Setup event listeners
    document.getElementById('searchInput').addEventListener('input', debounce(handleSearch, 300));
    document.getElementById('roleFilter').addEventListener('change', handleRoleFilter);
    document.getElementById('statusFilter').addEventListener('change', handleStatusFilter);
    document.getElementById('perPageSelect').addEventListener('change', handlePerPageChange);
    
    // Setup form listeners
    document.getElementById('createUserForm').addEventListener('submit', handleCreateUser);
    document.getElementById('editUserForm').addEventListener('submit', handleEditUser);
});

async function loadUsers(page = 1) {
    try {
        const params = new URLSearchParams({
            page: page,
            per_page: document.getElementById('perPageSelect').value,
            ...currentFilters
        });
        
        const response = await fetch(`/api/users?${params}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            const usersData = data.data?.data || data.data || [];
            renderUsersTable(usersData);
            renderPagination(data.data || data);
        } else {
            throw new Error('Error al cargar usuarios');
        }
    } catch (error) {
        console.error('Error:', error);
        showErrorState();
    }
}

async function loadUsersStats() {
    try {
        const response = await fetch('/api/users/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const stats = await response.json();
            
            // Update main stats - match API response structure
            document.getElementById('totalUsers').textContent = stats.total || 0;
            document.getElementById('activeUsers').textContent = stats.active || 0;
            document.getElementById('recentLogins').textContent = stats.recent || 0;
            document.getElementById('suspendedUsers').textContent = stats.suspended || 0;
            
            // Update role counts - API returns 'by_role', not 'roles'
            if (stats.by_role) {
                document.getElementById('adminCount').textContent = stats.by_role.admin || 0;
                document.getElementById('doctorCount').textContent = stats.by_role.doctor || 0;
                document.getElementById('nurseCount').textContent = stats.by_role.nurse || 0;
                document.getElementById('receptionistCount').textContent = stats.by_role.receptionist || 0;
                document.getElementById('accountantCount').textContent = stats.by_role.accountant || 0;
                document.getElementById('labTechnicianCount').textContent = stats.by_role.lab_technician || 0;
            }
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

function renderUsersTable(users) {
    const container = document.getElementById('usersTableContainer');
    
    if (users.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
                <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                <h3 style="margin-bottom: 0.5rem;">No se encontraron usuarios</h3>
                <p>No hay usuarios registrados o que coincidan con los filtros.</p>
                <button class="btn btn-primary" onclick="showCreateUserModal()" style="margin-top: 1rem;">
                    <i class="fas fa-user-plus"></i>
                    Crear Primer Usuario
                </button>
            </div>
        `;
        return;
    }
    
    const table = `
        <table class="users-table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th>Fecha Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                ${users.map(user => `
                    <tr>
                        <td data-label="Usuario">
                            <div class="user-info">
                                <div class="user-avatar" style="background: ${getUserColor(user.name)};">
                                    ${getUserInitials(user.name)}
                                </div>
                                <div class="user-details">
                                    <h4>${user.name}</h4>
                                    <p>${user.email}</p>
                                </div>
                            </div>
                        </td>
                        <td data-label="Rol">
                            <span class="role-badge role-${user.role}">
                                <i class="${getRoleIcon(user.role)}"></i>
                                ${formatRole(user.role)}
                            </span>
                        </td>
                        <td data-label="Estado">
                            <span class="role-badge status-${user.status}">
                                <i class="${getStatusIcon(user.status)}"></i>
                                ${formatStatus(user.status)}
                            </span>
                        </td>
                        <td data-label="Último Acceso">
                            ${formatLastLogin(user.last_login)}
                        </td>
                        <td data-label="Fecha Registro">
                            ${formatDate(user.created_at)}
                        </td>
                        <td data-label="Acciones">
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="showUserActions(${user.id})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;
    
    container.innerHTML = table;
}

function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    
    if (!data.last_page || data.last_page <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let pagination = '<div class="pagination">';
    
    if (data.current_page > 1) {
        pagination += `<button class="pagination-btn" onclick="loadUsers(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;
    }
    
    const startPage = Math.max(1, data.current_page - 2);
    const endPage = Math.min(data.last_page, data.current_page + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        pagination += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" 
            onclick="loadUsers(${i})">${i}</button>`;
    }
    
    if (data.current_page < data.last_page) {
        pagination += `<button class="pagination-btn" onclick="loadUsers(${data.current_page + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>`;
    }
    
    pagination += '</div>';
    container.innerHTML = pagination;
}

// Event Handlers
function handleSearch(event) {
    const searchTerm = event.target.value.trim();
    if (searchTerm) {
        currentFilters.search = searchTerm;
    } else {
        delete currentFilters.search;
    }
    loadUsers(1);
}

function handleRoleFilter(event) {
    const role = event.target.value;
    if (role) {
        currentFilters.role = role;
    } else {
        delete currentFilters.role;
    }
    loadUsers(1);
}

function handleStatusFilter(event) {
    const status = event.target.value;
    if (status) {
        currentFilters.status = status;
    } else {
        delete currentFilters.status;
    }
    loadUsers(1);
}

function handlePerPageChange() {
    loadUsers(1);
}

function clearFilters() {
    currentFilters = {};
    document.getElementById('searchInput').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    loadUsers(1);
}

// Modal Functions
function showCreateUserModal() {
    document.getElementById('createUserModal').style.display = 'flex';
}

function closeCreateUserModal() {
    document.getElementById('createUserModal').style.display = 'none';
    document.getElementById('createUserForm').reset();
}

function showEditUserModal(user) {
    const form = document.getElementById('editUserForm');
    form.user_id.value = user.id;
    form.name.value = user.name;
    form.email.value = user.email;
    form.role.value = user.role;
    form.status.value = user.status;
    form.password.value = '';
    
    document.getElementById('editUserModal').style.display = 'flex';
}

function closeEditUserModal() {
    document.getElementById('editUserModal').style.display = 'none';
    document.getElementById('editUserForm').reset();
}

async function showRolesModal() {
    document.getElementById('rolesModal').style.display = 'flex';
    
    try {
        const response = await fetch('/api/users/roles', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const roles = await response.json();
            renderRolesAndPermissions(roles);
        }
    } catch (error) {
        console.error('Error loading roles:', error);
    }
}

function closeRolesModal() {
    document.getElementById('rolesModal').style.display = 'none';
}

function renderRolesAndPermissions(roles) {
    const container = document.getElementById('rolesContainer');
    
    let html = '';
    Object.entries(roles).forEach(([roleKey, roleData]) => {
        html += `
            <div class="role-card">
                <h4>
                    <i class="${getRoleIcon(roleKey)}"></i>
                    ${roleData.name}
                </h4>
                <p>${roleData.description}</p>
                <div class="permissions-grid">
                    ${roleData.permissions.map(permission => 
                        `<div class="permission-item">${permission}</div>`
                    ).join('')}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Form Handlers
async function handleCreateUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const userData = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/api/users', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(userData)
        });
        
        if (response.ok) {
            closeCreateUserModal();
            loadUsers();
            loadUsersStats();
            alert('Usuario creado exitosamente');
        } else {
            const error = await response.json();
            alert('Error al crear usuario: ' + (error.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al crear usuario');
    }
}

async function handleEditUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const userData = Object.fromEntries(formData);
    const userId = userData.user_id;
    delete userData.user_id;
    
    // Remove empty password
    if (!userData.password) {
        delete userData.password;
    }
    
    try {
        const response = await fetch(`/api/users/${userId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify(userData)
        });
        
        if (response.ok) {
            closeEditUserModal();
            loadUsers();
            loadUsersStats();
            alert('Usuario actualizado exitosamente');
        } else {
            const error = await response.json();
            alert('Error al actualizar usuario: ' + (error.message || 'Error desconocido'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al actualizar usuario');
    }
}

function showUserActions(userId) {
    const actions = [
        `Ver perfil: /users/${userId}`,
        `Editar usuario`,
        `Cambiar estado`,
        `Restablecer contraseña`,
        `Ver actividad`,
        `Eliminar usuario`
    ];
    
    // Simple implementation - in production, use a proper dropdown menu
    const action = prompt('Seleccione una acción:\n' + actions.map((a, i) => `${i + 1}. ${a}`).join('\n'));
    
    if (action === '2') {
        // Edit user - fetch user data first
        fetch(`/api/users/${userId}`)
            .then(response => response.json())
            .then(user => showEditUserModal(user))
            .catch(error => console.error('Error:', error));
    }
}

function exportUsers() {
    alert('Función de exportación en desarrollo');
}

function showErrorState() {
    document.getElementById('usersTableContainer').innerHTML = `
        <div style="text-align: center; padding: 3rem; color: var(--gray-500);">
            <i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);"></i>
            <h3 style="margin-bottom: 0.5rem;">Error al cargar usuarios</h3>
            <p>Hubo un problema al cargar la información. Por favor, intenta de nuevo.</p>
            <button class="btn btn-primary" onclick="loadUsers()" style="margin-top: 1rem;">
                <i class="fas fa-redo"></i>
                Reintentar
            </button>
        </div>
    `;
}

// Utility functions
function getUserColor(name) {
    const colors = ['#667eea', '#764ba2', '#00AEEF', '#FF6B6B', '#10B981', '#F59E0B', '#8B5CF6'];
    const hash = name.split('').reduce((a, b) => {
        a = ((a << 5) - a) + b.charCodeAt(0);
        return a & a;
    }, 0);
    return colors[Math.abs(hash) % colors.length];
}

function getUserInitials(name) {
    if (!name) return 'U';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return parts[0].charAt(0).toUpperCase() + parts[1].charAt(0).toUpperCase();
    }
    return parts[0].charAt(0).toUpperCase();
}

function getRoleIcon(role) {
    const icons = {
        'admin': 'fas fa-crown',
        'doctor': 'fas fa-user-md',
        'nurse': 'fas fa-user-nurse',
        'receptionist': 'fas fa-user-tie',
        'accountant': 'fas fa-calculator',
        'lab_technician': 'fas fa-microscope'
    };
    return icons[role] || 'fas fa-user';
}

function formatRole(role) {
    const roles = {
        'admin': 'Administrador',
        'doctor': 'Doctor',
        'nurse': 'Enfermera',
        'receptionist': 'Recepcionista',
        'accountant': 'Contador',
        'lab_technician': 'Técnico Lab'
    };
    return roles[role] || role;
}

function getStatusIcon(status) {
    const icons = {
        'active': 'fas fa-check-circle',
        'inactive': 'fas fa-pause-circle',
        'suspended': 'fas fa-ban'
    };
    return icons[status] || 'fas fa-question-circle';
}

function formatStatus(status) {
    const statuses = {
        'active': 'Activo',
        'inactive': 'Inactivo',
        'suspended': 'Suspendido'
    };
    return statuses[status] || status;
}

function formatLastLogin(lastLogin) {
    if (!lastLogin) return 'Nunca';
    const date = new Date(lastLogin);
    return date.toLocaleDateString('es-ES') + ' ' + date.toLocaleTimeString('es-ES', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES');
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
</script>
@endpush 