<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PR Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .animate-delay-1 {
            animation-delay: 0.1s;
            animation-fill-mode: both;
        }
        
        .animate-delay-2 {
            animation-delay: 0.2s;
            animation-fill-mode: both;
        }
    </style>
</head>
<body class="min-h-screen bg-secondary-50 antialiased">
    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- LEFT SECTION - Illustration --}}
        <div class="hidden lg:flex flex-col justify-between p-12 bg-gradient-to-br from-primary-600 via-primary-500 to-primary-600 relative overflow-hidden">
            
            {{-- Background Pattern --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 40px 40px;"></div>
            </div>

            {{-- Logo & Brand --}}
            <div class="relative z-10 animate-fade-in">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white">PR Generator</h1>
                </div>
                <p class="text-primary-100 text-sm">Purchase Requisition Management System</p>
            </div>

            {{-- Main Content --}}
            <div class="relative z-10 animate-fade-in animate-delay-1">
                <h2 class="text-4xl font-bold text-white mb-4 leading-tight">
                    Simplify Your<br>
                    Purchase Workflow
                </h2>
                <p class="text-primary-50 text-lg max-w-md leading-relaxed">
                    Manage purchase requisitions efficiently with our modern, streamlined platform designed for digital marketing teams.
                </p>
                
                {{-- Features --}}
                <div class="mt-8 space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 bg-white/20 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">Digital Approval Workflow</p>
                            <p class="text-primary-100 text-xs mt-0.5">Streamlined approval process with digital signatures</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 bg-white/20 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">Real-time Tracking</p>
                            <p class="text-primary-100 text-xs mt-0.5">Monitor PR status from draft to payment completion</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-5 h-5 bg-white/20 rounded flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white font-medium text-sm">Secure & Reliable</p>
                            <p class="text-primary-100 text-xs mt-0.5">Role-based access control with activity logging</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="relative z-10 text-primary-100 text-xs animate-fade-in animate-delay-2">
                <p>© {{ date('Y') }} PR Generator. All rights reserved.</p>
            </div>
        </div>

        {{-- RIGHT SECTION - Login Form --}}
        <div class="flex items-center justify-center p-6 lg:p-12">
            <div class="w-full max-w-md">

                {{-- Mobile Logo --}}
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-primary-600 rounded-lg flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-bold text-secondary-900">PR Generator</h1>
                    </div>
                    <p class="text-secondary-600 text-sm">Purchase Requisition Management</p>
                </div>

                {{-- Login Card --}}
                <div class="animate-fade-in">
                    
                    {{-- Header --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-secondary-900 mb-2">Welcome back</h2>
                        <p class="text-secondary-600 text-sm">Enter your credentials to access your account</p>
                    </div>

                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-red-800 mb-1">Authentication failed</p>
                                    <ul class="text-sm text-red-700 space-y-0.5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Login Form --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">
                                Email Address
                            </label>
                            <input type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-3 bg-white border border-secondary-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors text-sm"
                                   placeholder="name@company.com"
                                   required
                                   autofocus>
                        </div>

                        {{-- Password --}}
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">
                                Password
                            </label>
                            <input type="password"
                                   name="password"
                                   class="w-full px-4 py-3 bg-white border border-secondary-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors text-sm"
                                   placeholder="Enter your password"
                                   required>
                        </div>

                        {{-- Remember & Forgot --}}
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox"
                                       id="remember"
                                       name="remember"
                                       class="w-4 h-4 text-primary-600 border-secondary-300 rounded focus:ring-2 focus:ring-primary-500">
                                <span class="text-sm text-secondary-700">Remember me</span>
                            </label>
                            
                            <a href="#" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                Forgot password?
                            </a>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="w-full px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm shadow-sm hover:shadow-md">
                            Sign in to your account
                        </button>
                    </form>

                    {{-- Divider --}}
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-secondary-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-3 bg-secondary-50 text-secondary-500 font-medium uppercase tracking-wide">
                                Demo Accounts
                            </span>
                        </div>
                    </div>

                    {{-- Demo Credentials --}}
                    <div class="space-y-2">
                        @php
                            $demoAccounts = [
                                ['role' => 'Super Admin', 'email' => 'superadmin@company.com', 'color' => 'purple'],
                                ['role' => 'Manager', 'email' => 'manager@company.com', 'color' => 'blue'],
                                ['role' => 'Staff', 'email' => 'staff1@company.com', 'color' => 'green'],
                            ];
                        @endphp

                        @foreach ($demoAccounts as $account)
                            <div class="flex items-center justify-between p-3 bg-white border border-secondary-200 rounded-lg hover:border-primary-300 hover:bg-primary-50/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-{{ $account['color'] }}-100 text-{{ $account['color'] }}-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-secondary-900">{{ $account['role'] }}</p>
                                        <p class="text-xs text-secondary-500">{{ $account['email'] }}</p>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="document.querySelector('input[name=email]').value='{{ $account['email'] }}'; document.querySelector('input[name=password]').value='password';"
                                        class="text-xs font-medium text-primary-600 hover:text-primary-700 px-3 py-1.5 rounded-lg hover:bg-primary-50 transition-colors">
                                    Use
                                </button>
                            </div>
                        @endforeach

                        <p class="text-center text-xs text-secondary-500 mt-3">
                            All demo accounts use password: <code class="px-2 py-0.5 bg-primary-50 text-primary-700 rounded font-mono text-xs font-semibold">password</code>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>