<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PR Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-primary-50">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

        {{-- LEFT SECTION --}}
        <div class="hidden lg:flex flex-col justify-center items-center relative p-10 m-10 rounded-3xl bg-white overflow-hidden shadow-[0_8px_30px_rgba(0,0,0,0.06)]">

            {{-- Soft Background Accent --}}
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute top-0 right-0 w-72 h-72 bg-primary-300/30 rounded-full blur-3xl translate-x-10 -translate-y-10"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-orange-300/30 rounded-full blur-3xl -translate-x-10 translate-y-10"></div>
            </div>

            {{-- Main Content --}}
            <div class="relative max-w-md text-center px-10">

                {{-- Illustration --}}
                <img 
                    src="https://tokpee.co/blog/wp-content/uploads/2025/05/A_flat-style_digital_illustration_depicts_a_young_.webp"
                    class="w-80 mx-auto rounded-xl shadow-xl animate-fade-in hover:scale-[1.02] transition-transform duration-300"
                    alt="Login Illustration"
                >

                {{-- Title --}}
                <h2 class="text-3xl font-heading font-bold text-secondary-900 mt-10 tracking-tight animate-slide-in">
                    PR Generator System
                </h2>

                {{-- Subtitle --}}
                <p class="text-secondary-600 leading-relaxed text-sm mt-3 max-w-sm mx-auto animate-fade-in delay-150">
                    Platform modern untuk mengelola Purchase Requisition lebih cepat, terstruktur, dan aman.
                </p>
            </div>

            {{-- Footer --}}
            <p class="absolute bottom-6 text-xs text-secondary-500">
                © {{ date('Y') }} PR Generator — All rights reserved.
            </p>
        </div>

        {{-- RIGHT SECTION (Authentication Form) --}}
        <div class="flex items-center justify-center p-6">

            <div class="w-full max-w-md">

                {{-- Logo --}}
                <div class="lg:hidden text-center mb-8 animate-fade-in">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl 
                                bg-gradient-to-br from-primary-500 to-primary-600 shadow-orange-sm mb-4">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-heading font-bold text-gradient mb-1">PR Generator</h1>
                    <p class="text-secondary-600">Purchase Requisition Management System</p>
                </div>

                {{-- Login Card --}}
                <div class="card-orange animate-slide-in">
                    <div class="card-body">
                        <h2 class="text-primary-500 text-[24px] text-center font-bold">Login ke Akun Anda</h2>
                        {{-- ERROR --}}
                        @if ($errors->any())
                            <div class="alert-danger mb-6">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-semibold">Terjadi kesalahan!</p>
                                    <ul class="mt-1 text-sm list-disc list-inside">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        {{-- Form --}}
                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                    Email Address
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <x-icon name="mail" class="w-5 h-5 text-secondary-400" />
                                    </div>
                                    <input type="email"
                                           name="email"
                                           class="input pl-11"
                                           placeholder="nama@company.com"
                                           required>
                                </div>
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-secondary-900 mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <x-icon name="lock" class="w-5 h-5 text-secondary-400" />
                                    </div>
                                    <input type="password"
                                           name="password"
                                           class="input pl-11"
                                           placeholder="•••••••"
                                           required>
                                </div>
                            </div>

                            {{-- Remember --}}
                            <div class="flex items-center">
                                <input type="checkbox"
                                       id="remember"
                                       name="remember"
                                       class="w-4 h-4 text-primary-600 border-secondary-300 rounded focus:ring-primary-500">
                                <label for="remember" class="ml-2 text-sm text-secondary-700 font-medium">
                                    Ingat saya
                                </label>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" class="btn-primary w-full text-base py-3">
                                Login
                            </button>
                        </form>

                        {{-- Divider --}}
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-secondary-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-3 bg-white text-secondary-500 font-medium">Demo Credentials</span>
                            </div>
                        </div>

                        {{-- Demo accounts --}}
                        <div class="space-y-2 text-xs">
                            @foreach ([
                                'Super Admin' => 'superadmin@company.com',
                                'Manager'     => 'manager@company.com',
                                'Staff'       => 'staff1@company.com',
                            ] as $role => $email)
                                <div class="flex items-center justify-between p-3 bg-orange-light-50 rounded-lg border border-primary-200">
                                    <span class="font-semibold text-secondary-700">{{ $role }}:</span>
                                    <code class="text-primary-600 font-semibold">{{ $email }}</code>
                                </div>
                            @endforeach

                            <p class="text-center text-secondary-500 mt-2">
                                Password: <code class="font-semibold">password</code>
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>