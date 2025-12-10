<div
    x-data="{ show: @entangle('isOpen') }" 
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed inset-0 z-[60]" 
    aria-labelledby="chat-modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    
    <div 
        x-on:click="show = false; $wire.closeModal()" 
        class="fixed inset-0 bg-white/5 backdrop-blur-sm transition-opacity">
    </div>

    <div 
        x-on:click.outside="show = false; $wire.closeModal()"
        class="fixed bottom-[4.5rem] right-6 w-full max-w-sm h-[70vh] flex flex-col z-[70] shadow-2xl"
    > 
        
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left w-full h-full flex flex-col">
            
            {{-- CHAT HEADER --}}
            <div class="flex items-center justify-between p-4 bg-black text-white rounded-t-lg shadow-md">
                <h3 class="text-lg font-semibold leading-6" id="chat-modal-title">
                    Live Chat Assistant 🤖
                </h3>
                <button
                    type="button"
                    x-on:click="show = false; $wire.closeModal()"
                    class="text-gray-300 hover:text-white transition"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            {{-- CHAT MESSAGE HISTORY --}}
            <div 
                x-data="{}"
                x-init="() => {
                    Livewire.on('chatUpdated', () => {
                        $el.scrollTop = $el.scrollHeight;
                    });
                    $el.scrollTop = $el.scrollHeight;
                }"
                class="flex-grow p-4 overflow-y-auto bg-gray-50 space-y-4"
            >
                @foreach ($messages as $index => $msg)
                    {{-- FILTER KRITIS: MELEWATI PESAN PERTAMA (INDEKS 0) YANG MERUPAKAN INSTRUKSI SISTEM/FAQ --}}
                    @if ($index === 0)
                        @continue 
                    @endif
                    
                    <div class="flex @if ($msg['role'] === 'user') justify-end @else justify-start @endif">
                        <div class="p-3 rounded-lg max-w-[80%] @if ($msg['role'] === 'user') bg-black text-white @else bg-gray-200 text-gray-800 @endif">
                            <p class="text-sm">{!! nl2br(e($msg['text'])) !!}</p>
                        </div>
                    </div>
                @endforeach

                {{-- PESAN LOADING ASYNCRONOUS DARI AI --}}
                <div wire:loading wire:target="sendMessage">
                    <div class="flex justify-start">
                        <div class="bg-gray-300 p-3 rounded-lg max-w-[80%]">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700">Asisten sedang mengetik...</span>
                                <svg class="animate-pulse h-3 w-3 text-gray-600" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($needsConfirmation)
                    <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg space-y-3">
                        <h4 class="font-semibold text-sm text-yellow-800">
                            ⚠️ Konfirmasi Pembuatan Tiket
                        </h4>
                        <p class="text-xs text-gray-700">
                            Pertanyaan Anda ("{{ $ticketSummary }}") di luar cakupan FAQ. Apakah Anda ingin mengajukannya sebagai tiket dukungan?
                        </p>
                        
                        {{-- PILIHAN DEPARTEMEN --}}
                        <div>
                            <label for="department_select" class="block text-xs font-medium text-gray-700 mb-1">
                                Tujukan ke Departemen:
                            </label>
                            <select 
                                wire:model="selectedDepartmentId" 
                                id="department_select"
                                class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:border-black focus:ring-black"
                            >
                                @foreach ($availableDepartments as $dept)
                                    <option value="{{ $dept['department_id'] }}">{{ $dept['department_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        {{-- TOMBOL TINDAKAN --}}
                        <div class="flex space-x-2 pt-2">
                            <button 
                                wire:click="confirmTicket"
                                wire:loading.attr="disabled"
                                class="flex-1 px-3 py-1 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 disabled:opacity-50 transition"
                            >
                                Konfirmasi & Buat Tiket
                            </button>
                            <button 
                                wire:click="cancelTicket"
                                class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition"
                            >
                                Batalkan
                            </button>
                        </div>
                    </div>
                @endif
                
            </div>
            
            {{-- CHAT INPUT AREA --}}
            <div class="p-4 border-t border-gray-200 bg-white">
                <form wire:submit="sendMessage"> 
                    <div class="flex items-center">
                        <input
                            type="text"
                            wire:model="currentMessage"
                            placeholder="{{ $needsConfirmation ? 'Tekan Kirim untuk melanjutkan pesan...' : 'Ketik pesan Anda...' }}"
                            @if ($needsConfirmation) disabled @endif
                            class="flex-grow border border-gray-300 rounded-lg p-2 focus:ring-black focus:border-black transition disabled:bg-gray-100"
                        >
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage"
                            class="ml-2 bg-black hover:bg-gray-800 text-white p-2 rounded-lg transition-colors disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="sendMessage">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                            <span wire:loading wire:target="sendMessage">
                                <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>