<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $statusFilter = '';
    public $perPage = 10;

    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showResetPasswordModal = false;

    // Form fields
    public $userId;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $selectedRole;
    public $is_active = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->roleFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    // ===============================================
    // CREATE USER
    // ===============================================
    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function createUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'selectedRole' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'selectedRole.required' => 'Role harus dipilih',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active,
        ]);

        $user->assignRole($this->selectedRole);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log("User created: {$user->name} with role {$this->selectedRole}");

        session()->flash('success', "User {$user->name} berhasil dibuat");
        $this->closeCreateModal();
    }

    // ===============================================
    // EDIT USER
    // ===============================================
    public function openEditModal($id)
    {
        $user = User::findOrFail($id);

        // Prevent editing super_admin by non-super_admin
        if ($user->hasRole('super_admin') && !Auth::user()->hasRole('super_admin')) {
            session()->flash('error', 'Anda tidak bisa edit Super Admin');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRole = $user->roles->first()?->name;
        $this->is_active = $user->is_active;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function updateUser()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->userId)],
            'selectedRole' => 'required|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        $user = User::findOrFail($this->userId);

        // Prevent editing super_admin
        if ($user->hasRole('super_admin') && !Auth::user()->hasRole('super_admin')) {
            session()->flash('error', 'Anda tidak bisa edit Super Admin');
            return;
        }

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ]);

        // Update role
        $user->syncRoles([$this->selectedRole]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log("User updated: {$user->name}");

        session()->flash('success', "User {$user->name} berhasil diupdate");
        $this->closeEditModal();
    }

    // ===============================================
    // DELETE USER
    // ===============================================
    public function openDeleteModal($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting super_admin
        if ($user->hasRole('super_admin')) {
            session()->flash('error', 'Super Admin tidak bisa dihapus');
            return;
        }

        // Prevent deleting self
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Anda tidak bisa menghapus akun sendiri');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    public function deleteUser()
    {
        $user = User::findOrFail($this->userId);

        // Prevent deleting super_admin
        if ($user->hasRole('super_admin')) {
            session()->flash('error', 'Super Admin tidak bisa dihapus');
            return;
        }

        // Prevent deleting self
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Anda tidak bisa menghapus akun sendiri');
            return;
        }

        $userName = $user->name;

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log("User deleted: {$userName}");

        $user->delete();

        session()->flash('success', "User {$userName} berhasil dihapus");
        $this->closeDeleteModal();
    }

    // ===============================================
    // RESET PASSWORD
    // ===============================================
    public function openResetPasswordModal($id)
    {
        $user = User::findOrFail($id);

        // Prevent resetting super_admin password by non-super_admin
        if ($user->hasRole('super_admin') && !Auth::user()->hasRole('super_admin')) {
            session()->flash('error', 'Anda tidak bisa reset password Super Admin');
            return;
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showResetPasswordModal = true;
    }

    public function closeResetPasswordModal()
    {
        $this->showResetPasswordModal = false;
        $this->resetForm();
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = User::findOrFail($this->userId);

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log("Password reset for user: {$user->name}");

        session()->flash('success', "Password untuk {$user->name} berhasil direset");
        $this->closeResetPasswordModal();
    }

    // ===============================================
    // TOGGLE STATUS
    // ===============================================
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Prevent deactivating super_admin
        if ($user->hasRole('super_admin')) {
            session()->flash('error', 'Super Admin tidak bisa dinonaktifkan');
            return;
        }

        // Prevent deactivating self
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Anda tidak bisa menonaktifkan akun sendiri');
            return;
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        activity()
            ->causedBy(Auth::user())
            ->performedOn($user)
            ->log("User {$status}: {$user->name}");

        session()->flash('success', "User {$user->name} berhasil {$status}");
    }

    // ===============================================
    // HELPER
    // ===============================================
    private function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRole = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $query = User::with('roles')
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->roleFilter, function ($q) {
                $q->role($this->roleFilter);
            })
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('is_active', $this->statusFilter);
            })
            ->latest();

        $users = $query->paginate($this->perPage);
        $roles = Role::all();

        // Stats
        $stats = [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'staff' => User::role('staff')->count(),
            'manager' => User::role('manager')->count(),
        ];

        return view('livewire.user-management', [
            'users' => $users,
            'roles' => $roles,
            'stats' => $stats,
        ])->layout('components.layouts.app', ['title' => 'User Management']);
    }
}