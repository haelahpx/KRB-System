<footer class="bg-white">
    <div class="mx-auto max-w-7xl px-6 sm:px-8">

        {{-- Data Retrieval --}}
        @php
        $company = auth()->user()?->company;
        $rawLogo = $company?->image;
        // Changed fallback path to match the asset path used in the logo image
        $fallback = asset('https://tiketkebunraya.id/assets/images/kebun-raya.png');
        $logoUrl = $fallback;

        if (!empty($rawLogo)) {
        if (preg_match('#^https?://#i', $rawLogo)) {
        $logoUrl = $rawLogo;
        } else {
        if (file_exists(public_path($rawLogo))) {
        $logoUrl = asset($rawLogo);
        } elseif (file_exists(public_path('storage/'.$rawLogo))) {
        $logoUrl = asset('storage/'.$rawLogo);
        } elseif (file_exists(public_path('images/'.$rawLogo))) {
        $logoUrl = asset('images/'.$rawLogo);
        }
        }
        }
        @endphp

        {{-- Desktop Footer --}}
        <div class="hidden md:grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-10 py-10">
            {{-- Logo --}}
            <div class="flex items-start">
                <a href="{{ route('home') }}" class="transition-transform duration-300 hover:scale-105 flex items-center gap-2">
                    <img src="{{ $logoUrl }}" alt="{{ $company?->company_name ?? 'Kebun Raya' }} Logo" class="h-20 w-auto">
                </a>
            </div>

            {{-- Tagline + Socials --}}
            <div class="md:col-span-2 lg:col-span-1">
                <p class="text-sm leading-6 text-slate-500 max-w-[32ch] " style="text-align: justify;">
                    Kebun Raya berpegang pada lima pilar: Konservasi (melestarikan tumbuhan), Edukasi (meningkatkan pengetahuan botani), Penelitian, Jasa Lingkungan, dan Wisata Alam yang inspiratif.
                </p>
                <div class="mt-6 flex items-center gap-4">
                    {{-- Social icons --}}
                </div>
            </div>

            {{-- Nav links --}}
            <nav class="lg:pl-6">
                <ul class="space-y-3 text-sm text-slate-500">
                    <li><a href="#" class="hover:text-slate-700 transition-colors">About Us</a></li>
                    <li><a href="#" class="hover:text-slate-700 transition-colors">Services</a></li>
                    <li><a href="#" class="hover:text-slate-700 transition-colors">Portfolio</a></li>
                    <li><a href="#" class="hover:text-slate-700 transition-colors">Blog</a></li>
                    <li><a href="#" class="hover:text-slate-700 transition-colors">Contact</a></li>
                </ul>
            </nav>

            {{-- Contact --}}
            <div class="space-y-3 text-sm text-slate-500">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-envelope class="mt-0.5 h-5 w-5" />
                    <span>{{ $company?->company_email ?? 'gatau@mauisiapa.com' }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <x-heroicon-o-phone class="mt-0.5 h-5 w-5" />
                    <span>{{ $company?->company_phone ?? '(0251) 8311362' }}</span>
                </div>
                <div class="flex items-start gap-3">
                    <x-heroicon-o-map-pin class="mt-0.5 h-5 w-5" />
                    <span>{{ $company?->company_address ?? 'Jl. Ir. H. Juanda No.13, Paledang, Kecamatan Bogor Tengah, Kota Bogor, Jawa Barat 16122' }}</span>
                </div>
            </div>
        </div>

        {{-- Mobile Footer --}}
        <div class="block md:hidden py-10 flex flex-col gap-10">
            {{-- Logo (centered) --}}
            <div class="flex justify-center">
                <a href="{{ route('home') }}" class="transition-transform duration-300 hover:scale-105 flex items-center gap-2">
                    <img src="{{ $logoUrl }}" alt="{{ $company?->company_name ?? 'Kebun Raya' }} Logo" class="h-20 w-auto">
                </a>
            </div>

            {{-- Tagline --}}
            <p class="text-sm leading-6 text-slate-500 max-w-[62ch] mx-auto" style="text-align: justify;">
                Kebun Raya berpegang pada lima pilar: Konservasi (melestarikan tumbuhan), Edukasi (meningkatkan pengetahuan botani), Penelitian, Jasa Lingkungan, dan Wisata Alam yang inspiratif.
            </p>

            {{-- Socials (centered) --}}
            <div class="flex justify-center gap-4">
                {{-- Social icons --}}
            </div>

            {{-- Nav + Contact (same row) --}}
            <div class="flex justify-between gap-4">
                {{-- Nav links --}}
                <nav>
                    <ul class="flex flex-col gap-2 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-slate-700 transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-slate-700 transition-colors">Services</a></li>
                        <li><a href="#" class="hover:text-slate-700 transition-colors">Portfolio</a></li>
                        <li><a href="#" class="hover:text-slate-700 transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-slate-700 transition-colors">Contact</a></li>
                    </ul>
                </nav>

                {{-- Contact --}}
                <div class="flex flex-col gap-2 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-envelope class="h-5 w-5" />
                        <span>{{ $company?->company_email ?? 'gatau@mauisiapa.com' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-phone class="h-5 w-5" />
                        <span>{{ $company?->company_phone ?? '(0251) 8311362' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-map-pin class="h-5 w-5" />
                        <span>{{ $company?->company_address ?? 'Jl. Ir. H. Juanda No.13, Paledang, Kecamatan Bogor Tengah, Kota Bogor, Jawa Barat 16122' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-slate-200"></div>

        {{-- Bottom bar --}}
        <div class="flex flex-col sm:flex-row items-center justify-between py-4 text-xs text-slate-400 gap-4">
            <p>© 2024 {{ $company?->company_name ?? 'YourBrand' }}. All rights reserved.</p>
            <ul class="flex items-center gap-8">
                <li><a href="#" class="hover:text-slate-600">Cookie Policy</a></li>
                <li><a href="#" class="hover:text-slate-600">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-slate-600">Terms of Service</a></li>
            </ul>
        </div>
    </div>
</footer>