<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Kos-Ku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-cover bg-center bg-fixed font-sans text-gray-800"
      style="background-image: url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1600&q=80')">
    <div class="absolute inset-0 bg-slate-950/55 backdrop-blur-[2px]"></div>

    <main class="relative flex min-h-screen items-center justify-center px-4 py-10">
        <section class="w-full max-w-md rounded-3xl border border-white/60 bg-white/95 p-7 shadow-2xl backdrop-blur-md sm:p-10">
            <a href="{{ route('home') }}" class="mb-7 inline-flex items-center gap-2 text-sm font-medium text-gray-500 transition hover:text-blue-700">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Beranda
            </a>

            <div class="mb-7 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-3xl text-blue-700">
                    <i class="fa-solid fa-house-user"></i>
                </div>
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-blue-700">Kos-Ku Smart Living</p>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Selamat Datang</h1>
                <p class="mt-2 text-sm text-gray-500">Masuk untuk memilih dan memesan kamar kos.</p>
            </div>

            @if (session('status'))
                <div role="status" class="mb-5 rounded-xl border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (filled(config('services.google.client_id')))
                <form id="google-login-form" method="POST" action="{{ route('google.callback', absolute: false) }}" class="mb-5">
                    @csrf
                    <input id="google-credential" type="hidden" name="credential">
                    <div id="google-signin-button" class="flex justify-center"></div>
                </form>

                <script>
                    function handleGoogleCredential(response) {
                        document.getElementById('google-credential').value = response.credential;
                        document.getElementById('google-login-form').submit();
                    }

                    function initializeGoogleSignIn() {
                        google.accounts.id.initialize({
                            client_id: @json(config('services.google.client_id')),
                            callback: handleGoogleCredential,
                            auto_select: false
                        });

                        google.accounts.id.renderButton(
                            document.getElementById('google-signin-button'),
                            { theme: 'outline', size: 'large', text: 'continue_with', shape: 'rectangular', width: 340 }
                        );
                    }
                </script>
                <script src="https://accounts.google.com/gsi/client" async defer onload="initializeGoogleSignIn()"></script>
            @else
                <div role="status" class="mb-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                    Login Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID di file .env.
                </div>
            @endif

            <div class="mb-5 flex items-center gap-3" aria-hidden="true">
                <div class="h-px flex-1 bg-gray-200"></div>
                <span class="text-xs font-medium text-gray-400">atau masuk dengan email</span>
                <div class="h-px flex-1 bg-gray-200"></div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-gray-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           autocomplete="username"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-1.5 block text-sm font-semibold text-gray-700">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label for="remember_me" class="flex items-center gap-2 text-sm text-gray-600">
                    <input id="remember_me" type="checkbox" name="remember" value="1"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    Ingat saya
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-blue-600 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-bold text-blue-700 hover:underline">Daftar sekarang</a>
            </p>
        </section>
    </main>
</body>
</html>
