<div class="space-y-6">

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="flex items-center gap-3 p-3 rounded-lg border border-green-200 bg-green-50 text-green-700 shadow-soft animate-fade-in">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="flex items-center gap-3 p-3 rounded-lg border border-red-200 bg-red-50 text-red-700 shadow-soft animate-fade-in">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-4">
            <p class="text-xs font-medium text-secondary-500">Total Users</p>
            <h3 class="text-2xl font-bold text-secondary-900 mt-1">{{ $stats['total'] }}</h3>
        </div>

        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-4">
            <p class="text-xs font-medium text-secondary-500">Active</p>
            <h3 class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active'] }}</h3>
        </div>

        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-4">
            <p class="text-xs font-medium text-secondary-500">Inactive</p>
            <h3 class="text-2xl font-bold text-red-600 mt-1">{{ $stats['inactive'] }}</h3>
        </div>

        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-4">
            <p class="text-xs font-medium text-secondary-500">Staff</p>
            <h3 class="text-2xl font-bold text-primary-600 mt-1">{{ $stats['staff'] }}</h3>
        </div>

        <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-4">
            <p class="text-xs font-medium text-secondary-500">Manager</p>
            <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['manager'] }}</h3>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rounded-xl border border-secondary-200 bg-white shadow-soft p-5 space-y-4">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-secondary-900">User Management</h3>
            
            @can('user.create')
                <button wire:click="openCreateModal" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add User
                </button>
            @endcan
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">Search</label>
                <input 
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="input bg-white border-secondary-200 focus:border-primary-400"
                    placeholder="Name or email..."
                >
            </div>

            {{-- Role Filter --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">Role</label>
                <select wire:model.live="roleFilter" class="input bg-white border-secondary-200 focus:border-primary-400">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">Status</label>
                <select wire:model.live="statusFilter" class="input bg-white border-secondary-200 focus:border-primary-400">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            {{-- Reset --}}
            <div class="flex flex-col gap-1.5">
                <label class="text-xs font-medium text-secondary-600">&nbsp;</label>
                <button wire:click="resetFilters" class="btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-secondary-200 bg-white shadow-soft">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=f97316&color=fff" 
                                        class="w-8 h-8 rounded-lg" alt="{{ $user->name }}">
                                    <div>
                                        <p class="font-semibold text-secondary-900">{{ $user->name }}</p>
                                        @if($user->id === Auth::id())
                                            <span class="text-xs text-primary-600">(You)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ ucfirst(str_replace('_', ' ', $user->roles->first()?->name ?? 'No Role')) }}
                                </span>
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-sm">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="text-sm">{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="flex justify-end items-center gap-2">
                                    {{-- Edit --}}
                                    @can('user.edit')
                                        <button 
                                            wire:click="openEditModal({{ $user->id }})"
                                            class="text-blue-600 hover:text-blue-800 p-1"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    @endcan

                                    {{-- Reset Password --}}
                                    @can('user.reset-password')
                                        <button 
                                            wire:click="openResetPasswordModal({{ $user->id }})"
                                            class="text-amber-600 hover:text-amber-800 p-1"
                                            title="Reset Password">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                            </svg>
                                        </button>
                                    @endcan

                                    {{-- Toggle Status --}}
                                    @can('user.activate')
                                        @if(!$user->hasRole('super_admin') && $user->id !== Auth::id())
                                            <button 
                                                wire:click="toggleStatus({{ $user->id }})"
                                                class="{{ $user->is_active ? 'text-red-600 hover:text-red-800' : 'text-green-600 hover:text-green-800' }} p-1"
                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                @if($user->is_active)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        @endif
                                    @endcan

                                    {{-- Delete --}}
                                    @can('user.delete')
                                        @if(!$user->hasRole('super_admin') && $user->id !== Auth::id())
                                            <button 
                                                wire:click="openDeleteModal({{ $user->id }})"
                                                class="text-red-600 hover:text-red-800 p-1"
                                                title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p class="font-semibold text-secondary-700">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-secondary-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
    {{-- CREATE MODAL --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl animate-scale-in">
                <div class="px-6 py-4 border-b border-primary-200 bg-primary-50 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-primary-700">Create New User</h3>
                </div>
                
                <form wire:submit.prevent="createUser" class="p-6 space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="name"
                            class="input @error('name') input-error @enderror"
                            placeholder="John Doe"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            wire:model="email"
                            class="input @error('email') input-error @enderror"
                            placeholder="john@company.com"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            wire:model="password"
                            class="input @error('password') input-error @enderror"
                            placeholder="Min 8 characters"
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            wire:model="password_confirmation"
                            class="input @error('password_confirmation') input-error @enderror"
                            placeholder="Confirm password"
                        >
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select 
                            wire:model="selectedRole"
                            class="input @error('selectedRole') input-error @enderror"
                        >
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                @if($role->name !== 'super_admin' || Auth::user()->hasRole('super_admin'))
                                    <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('selectedRole')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active Status --}}
                    <div class="flex items-center gap-2">
                        <input 
                            type="checkbox"
                            wire:model="is_active"
                            id="is_active_create"
                            class="w-4 h-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                        >
                        <label for="is_active_create" class="text-sm text-secondary-700">Active User</label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="closeCreateModal"
                            class="w-full btn-secondary">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="w-full btn-primary"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createUser">Create User</span>
                            <span wire:loading wire:target="createUser">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- EDIT MODAL --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl animate-scale-in">
                <div class="px-6 py-4 border-b border-blue-200 bg-blue-50 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-blue-700">Edit User</h3>
                </div>
                
                <form wire:submit.prevent="updateUser" class="p-6 space-y-4">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="name"
                            class="input @error('name') input-error @enderror"
                        >
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            wire:model="email"
                            class="input @error('email') input-error @enderror"
                        >
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select 
                            wire:model="selectedRole"
                            class="input @error('selectedRole') input-error @enderror"
                        >
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                @if($role->name !== 'super_admin' || Auth::user()->hasRole('super_admin'))
                                    <option value="{{ $role->name }}">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('selectedRole')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active Status --}}
                    <div class="flex items-center gap-2">
                        <input 
                            type="checkbox"
                            wire:model="is_active"
                            id="is_active_edit"
                            class="w-4 h-4 rounded border-secondary-300 text-primary-600 focus:ring-primary-500"
                        >
                        <label for="is_active_edit" class="text-sm text-secondary-700">Active User</label>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="closeEditModal"
                            class="w-full btn-secondary">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="w-full btn-primary"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="updateUser">Update User</span>
                            <span wire:loading wire:target="updateUser">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- DELETE MODAL --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl animate-scale-in">
                <div class="px-6 py-4 border-b border-red-200 bg-red-50 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-red-700">Delete User</h3>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex gap-3">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-secondary-900 font-semibold mb-1">Are you sure?</p>
                            <p class="text-sm text-secondary-600">
                                You are about to delete user <strong>{{ $name }}</strong>. This action cannot be undone.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="closeDeleteModal"
                            class="w-full btn-secondary">
                            Cancel
                        </button>
                        <button 
                            wire:click="deleteUser"
                            class="w-full btn-danger"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="deleteUser">Delete User</span>
                            <span wire:loading wire:target="deleteUser">Deleting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- RESET PASSWORD MODAL --}}
    @if($showResetPasswordModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm animate-fade-in">
            <div class="w-full max-w-md bg-white rounded-xl shadow-2xl animate-scale-in">
                <div class="px-6 py-4 border-b border-amber-200 bg-amber-50 rounded-t-xl">
                    <h3 class="text-lg font-semibold text-amber-700">Reset Password</h3>
                    <p class="text-xs text-amber-600 mt-1">Reset password for {{ $name }}</p>
                </div>
                
                <form wire:submit.prevent="resetPassword" class="p-6 space-y-4">
                    {{-- New Password --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            New Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            wire:model="password"
                            class="input @error('password') input-error @enderror"
                            placeholder="Min 8 characters"
                        >
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-medium text-secondary-800 mb-2">
                            Confirm New Password <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="password" 
                            wire:model="password_confirmation"
                            class="input @error('password_confirmation') input-error @enderror"
                            placeholder="Confirm new password"
                        >
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 pt-2">
                        <button 
                            type="button"
                            wire:click="closeResetPasswordModal"
                            class="w-full btn-secondary">
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="w-full btn-primary"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                            <span wire:loading wire:target="resetPassword">Resetting...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>