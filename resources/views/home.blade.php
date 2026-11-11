<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kos-Ku - Hunian Nyaman &amp; Strategis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans text-gray-800 overflow-x-hidden">
    <nav class="absolute top-0 left-0 w-full z-50 py-4 px-4 md:px-8 flex justify-between items-center">
        <h1 class="text-xl md:text-2xl font-extrabold text-white drop-shadow-md">
            Kos-Ku <span class="text-[10px] md:text-xs font-normal text-gray-200 block -mt-1">Smart Living</span>
        </h1>
        <div class="flex gap-2 md:gap-3">
            <a href="{{ route('login') }}" class="bg-blue-600 text-white text-xs md:text-sm font-semibold py-2 px-6 rounded-full hover:bg-blue-700 transition shadow-lg">
                Masuk
            </a>
        </div>
    </nav>

    <header class="relative h-screen flex items-center justify-center">
        <img
            src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=1600&q=80"
            alt="Kamar Kos"
            class="absolute inset-0 w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-black/50"></div>

        <div class="relative z-10 text-center px-4 max-w-3xl mt-10" data-aos="fade-up" data-aos-duration="1000">
            <span class="bg-blue-500 text-white text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-wider mb-6 inline-block shadow-lg">
                ✨ Fasilitas Premium
            </span>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight drop-shadow-lg">
                Temukan Kamar Kos Impianmu di Sini
            </h1>
            <p class="text-gray-200 text-sm md:text-xl mb-10 drop-shadow-md">
                Hunian nyaman, aman, dan strategis. Proses booking mudah dan cepat, langsung dari genggamanmu.
            </p>
            <a href="{{ route('booking.create') }}" class="bg-white text-blue-700 font-bold py-4 px-10 rounded-full text-lg hover:bg-gray-100 transition-all duration-300 hover:scale-105 shadow-2xl inline-block">
                Booking Sekarang <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </header>

    <section class="max-w-6xl mx-auto mt-16 md:mt-24 px-4 pb-20">
        <h3 class="text-2xl md:text-3xl font-bold text-center mb-10 text-gray-800" data-aos="fade-up">
            Mengapa Memilih Kos-Ku?
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 text-center">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all" data-aos="fade-up">
                <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=500&q=80" class="w-full h-48 object-cover" alt="Lokasi">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2 text-gray-800">Lokasi Strategis</h4>
                    <p class="text-gray-500 text-sm">Dekat dengan kampus dan akses transportasi umum.</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all" data-aos="fade-up" data-aos-delay="100">
                <img src="https://images.unsplash.com/photo-1558036117-15d82a90b9b1?w=500&q=80" class="w-full h-48 object-cover" alt="Keamanan">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2 text-gray-800">Keamanan 24 Jam</h4>
                    <p class="text-gray-500 text-sm">Dilengkapi CCTV dan akses smart lock.</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all" data-aos="fade-up" data-aos-delay="200">
                <img src="https://images.unsplash.com/photo-1505693314120-0d443867891c?w=500&q=80" class="w-full h-48 object-cover" alt="Fasilitas">
                <div class="p-6">
                    <h4 class="font-bold text-xl mb-2 text-gray-800">Fasilitas Lengkap</h4>
                    <p class="text-gray-500 text-sm">AC, WiFi, springbed, dan kamar mandi dalam.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-center py-8 text-gray-400 text-sm border-t bg-white">
        &copy; {{ now()->year }} Kos-Ku. All rights reserved.
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, offset: 50 });
    </script>
</body>
</html>
