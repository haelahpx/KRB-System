<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'App' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/kebun-raya-bogor.png') }}" />


    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-white min-h-screen" data-theme="light">

    @livewire('components.partials.navbar')

    <main class="container mx-auto pt-9 pb-4">
        {{ $slot }}
    </main>

    @livewire('components.ui.chat-modal')

    <div class="fixed bottom-6 right-6 z-[90]">
        <button
            x-data="{ isChatOpen: false }"
            x-on:chat-modal-status.window="isChatOpen = $event.detail.isOpen"
            x-on:click="Livewire.dispatchTo('components.ui.chat-modal', 'toggleChatModal')"
            class="bg-black hover:bg-gray-800 text-white p-3 rounded-full shadow-lg transition-all duration-300 hover:scale-110 group focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
            <x-heroicon-o-chat-bubble-left-right class="h-8 w-8" />
        </button>
    </div>
    
    @include('livewire.components.partials.footer')
    @livewire('components.ui.toast')


    <script>
        window.addEventListener('load', () => {
            console.log('[LAYOUT DEBUG] Livewire loaded:', !!window.Livewire);
        });

        document.addEventListener('openChatModal', function(e) {
            console.log('BROWSER EVENT (Window): Event \'openChatModal\' detected by a global listener.');
        });
    </script>
    @livewireScripts
</body>

</html>