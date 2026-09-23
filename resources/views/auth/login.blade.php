<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Inspection App</title>

    <!-- Tailwind CSS CDN (or use your local compiled Tailwind setup) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Font: Manrope -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Tailwind Config for Exact Colors -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif'],
                    },
                    colors: {
                        oasis: {
                            gold: '#C5A333',
                            green: '#336B3B',
                            greenHover: '#28542E',
                            bgLeft: '#F5F0E8',
                            bgRight: '#FAFAF8',
                            muted: '#8C8478',
                            dark: '#1A1A1A',
                            border: '#E5E2DC',
                            placeholder: '#B5B0A6'
                        }
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans antialiased bg-oasis-bgLeft md:bg-transparent">

    <div class="flex min-h-screen w-screen">

        <!-- ========================================== -->
        <!-- LEFT BRANDED PANEL (Desktop Only)           -->
        <!-- ========================================== -->
        <aside class="hidden md:flex flex-col justify-between items-center w-[50%] lg:w-[58%] bg-oasis-bgLeft px-12 lg:px-16 py-14 min-h-screen box-border">

            <!-- Top Accent Header -->
            <div class="flex flex-row justify-between items-center w-full">
                <span class="font-bold text-[12px] leading-4 uppercase tracking-[0.08em] text-oasis-gold">
                    Oasis Mitra Sarana
                </span>
                <div class="w-[60px] h-0 border-b-2 border-oasis-gold rounded-[1px]"></div>
            </div>

            <!-- Center Content (Logo & Tagline) -->
            <div class="flex flex-col items-center gap-5 text-center my-auto">
                <img class="w-[280px] max-w-full h-auto object-contain" src="/images/LogoOASIS.png" alt="Apartemen Oasis Mitra Sarana Logo">
                <p class="font-medium text-[15px] leading-[22px] text-oasis-muted tracking-[0.01em]">
                    Exquisite Living. Infinite Comfort.
                </p>
            </div>

            <!-- Bottom Copyright Text -->
            <div class="flex flex-col items-center gap-1.5 text-center">
                <p class="font-medium text-[13px] leading-[18px] text-oasis-muted">
                    Apartemen OASIS Mitra Sarana © 2024
                </p>
                <p class="font-normal text-[11px] leading-[15px] text-oasis-muted opacity-70">
                    Premium Resident Portal
                </p>
            </div>

        </aside>

        <!-- ========================================== -->
        <!-- RIGHT FORM PANEL (Mobile + Desktop)        -->
        <!-- ========================================== -->
        <main class="w-full md:w-[50%] lg:w-[42%] flex flex-col justify-between items-center bg-oasis-bgLeft md:bg-oasis-bgRight px-6 md:px-12 py-10 md:py-14 min-h-screen box-border">

            <div class="w-full max-w-[360px] my-auto">

                <!-- Mobile Header Logo & Subtitle (Shown ONLY on Mobile) -->
                <div class="flex md:hidden flex-col items-center gap-2 mb-9 text-center">
                    <img class="w-[180px] h-auto object-contain" src="/images/LogoOASIS.png" alt="OASIS Logo">
                    <span class="font-bold text-[11px] tracking-[0.08em] uppercase text-oasis-gold">
                        PORTAL INSPEKSI OASIS
                    </span>
                </div>

                <!-- Form Title Header -->
                <div class="mb-7 text-left">
                    <h1 class="font-bold text-[24px] md:text-[26px] leading-[34px] text-[#262626] mb-2">
                        Welcome Back
                    </h1>
                    <p class="font-normal text-[13px] leading-[18px] text-oasis-muted">
                        <span class="hidden md:inline">Please enter your credentials to access the OASIS portal.</span>
                        <span class="inline md:hidden">Enter your credentials to access the resident dashboard.</span>
                    </p>
                </div>

                <!-- Login Form -->
                <form class="flex flex-col gap-[18px]" action="{{ route('login.submit') }}" method="POST">
                    @csrf

                    <!-- Email / Username Input -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-[10px] leading-[14px] tracking-[0.06em] uppercase text-oasis-muted" for="login">
                            Email or Username
                        </label>
                        <div class="relative flex items-center">
                            <input
                                type="text"
                                id="login"
                                name="login"
                                class="w-full h-[44px] md:h-[44px] px-3.5 bg-white border border-oasis-border rounded-md text-[13px] text-oasis-dark placeholder:text-oasis-placeholder outline-none transition-all duration-200 focus:border-oasis-green focus:ring-2 focus:ring-oasis-green/10"
                                placeholder="name@oasisapartment.com"
                                required
                                autofocus>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-bold text-[10px] leading-[14px] tracking-[0.06em] uppercase text-oasis-muted" for="password">
                            Password
                        </label>
                        <div class="relative flex items-center">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full h-[44px] md:h-[44px] pl-3.5 pr-10 bg-white border border-oasis-border rounded-md text-[13px] text-oasis-dark placeholder:text-oasis-placeholder outline-none transition-all duration-200 focus:border-oasis-green focus:ring-2 focus:ring-oasis-green/10"
                                placeholder="••••••••"
                                required>
                            @error('login')
                            <span class="form-error">{{ $message }}</span>
                            @enderror

                            <button
                                type="button"
                                class="absolute right-3 bg-transparent border-none p-0 cursor-pointer text-oasis-muted hover:text-oasis-dark flex items-center justify-center"
                                onclick="togglePasswordVisibility()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Options: Remember Me & Forgot Password -->
                    <div class="flex justify-between items-center mt-0.5">
                        <label class="flex items-center gap-2 text-[13px] text-oasis-muted cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-[15px] h-[15px] accent-oasis-green rounded cursor-pointer">
                            <span>Remember <span class="hidden md:inline">me</span></span>
                        </label>
                        <a href="{{ route('password.request') }}" class="font-semibold text-[12px] text-oasis-gold hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full h-[46px] md:h-[46px] bg-oasis-green hover:bg-oasis-greenHover border-none rounded-md md:rounded-md font-semibold text-[14px] text-white cursor-pointer mt-1.5 transition-colors duration-200 shadow-sm">
                        Log In
                    </button>

                    <!-- Register Link -->
                    <p class="text-center text-[13px] text-oasis-muted mt-1">
                        Don't have an account? <a href="{{ route('register') }}" class="font-bold text-oasis-green hover:underline">Register</a>
                    </p>
                </form>

                <!-- Mobile Footer Link (Shown ONLY on Mobile) -->
                <div class="block md:hidden text-center mt-6">
                    <a href="#" class="text-[12px] text-oasis-muted hover:text-oasis-dark underline decoration-dotted">
                        Need assistance? Contact Management
                    </a>
                </div>

            </div>

            <!-- Desktop Bottom Footer Links (Hidden on Mobile) -->
            <div class="hidden md:flex items-center gap-2.5 text-[12px] text-oasis-muted">
                <a href="#" class="hover:text-oasis-dark transition-colors">Resident Support</a>
                <span>·</span>
                <a href="#" class="hover:text-oasis-dark transition-colors">Terms of Service</a>
            </div>

        </main>

    </div>

    <!-- Password Visibility Toggle Script -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
            } else {
                passwordInput.type = 'password';
            }
        }
    </script>
</body>

</html>