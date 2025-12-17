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
    role="dialog"
    aria-modal="true"
    style="display: none;">

    <div
        x-on:click="show = false"
        class="fixed inset-0 bg-white/5 backdrop-blur-sm transition-opacity">
    </div>

    <div
        class="fixed bottom-[4.5rem] right-6 w-full max-w-sm h-[70vh] flex flex-col z-[70] shadow-2xl">

        <div class="relative transform overflow-hidden rounded-lg bg-white text-left w-full h-full flex flex-col border border-gray-200">

            {{-- CHAT HEADER --}}
            <div class="flex items-center justify-between p-4 bg-black text-white rounded-t-lg shadow-md">
                <h3 class="text-lg font-semibold leading-6">
                    Kebun Raya AI Assistant
                </h3>
                <button
                    type="button"
                    x-on:click="show = false"
                    class="text-gray-300 hover:text-white transition">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- CHAT MESSAGE HISTORY --}}
            <div
                x-init="Livewire.on('chatUpdated', () => { $el.scrollTop = $el.scrollHeight; })"
                class="flex-grow p-4 overflow-y-auto bg-gray-50 space-y-4">
                
                @foreach ($messages as $index => $msg)
                    @if ($index === 0) @continue @endif

                    <div class="flex @if ($msg['role'] === 'user') justify-end @endif">
                        <div class="p-3 rounded-lg max-w-[85%] @if ($msg['role'] === 'user') bg-black text-white @else bg-white border border-gray-200 text-gray-800 shadow-sm @endif">
                            <p class="text-sm">{!! nl2br(e($msg['text'])) !!}</p>
                        </div>
                    </div>
                @endforeach

                {{-- LOADING INDICATOR --}}
                <div wire:loading wire:target="generateAiResponse">
                    <div class="flex justify-start">
                        <div class="bg-gray-200 p-3 rounded-lg flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Asisten sedang memproses...</span>
                            <div class="flex space-x-1">
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TICKET CONFIRMATION BOX --}}
                @if ($needsConfirmation)
                <div class="p-4 bg-white border border-gray-300 rounded-lg space-y-3 my-2 shadow-md animate-in fade-in slide-in-from-bottom-2">
                    <div class="flex items-center gap-2 border-b pb-2">
                        <span class="text-lg">🎫</span>
                        <h4 class="font-bold text-sm">Konfirmasi Tiket Otomatis</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] uppercase tracking-wider font-semibold">
                        <div class="bg-gray-50 p-2 rounded border">
                            <p class="text-gray-400">Departemen</p>
                            <p class="text-black">
                                {{ collect($availableDepartments)->firstWhere('department_id', $selectedDepartmentId)['department_name'] ?? 'Pilih Manual' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border">
                            <p class="text-gray-400">Prioritas AI</p>
                            <p class="{{ $ticketPriority === 'high' ? 'text-red-600' : 'text-blue-600' }}">
                                {{ strtoupper($ticketPriority) }}
                            </p>
                        </div>
                    </div>

                    <p class="text-xs italic text-gray-500">"{{ $aiReasoning }}"</p>

                    <div class="space-y-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase">Edit Kategori Jika Salah</label>
                        <select wire:model="selectedDepartmentId" class="w-full text-xs border-gray-200 rounded focus:ring-black">
                            @foreach ($availableDepartments as $dept)
                                <option value="{{ $dept['department_id'] }}">{{ $dept['department_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="confirmTicket" class="flex-1 bg-black text-white py-2 rounded text-xs font-bold hover:bg-gray-800 transition shadow-sm">
                            Buat Tiket
                        </button>
                        <button wire:click="cancelTicket" class="px-4 py-2 bg-gray-100 text-gray-700 rounded text-xs font-bold hover:bg-gray-200 transition">
                            Batal
                        </button>
                    </div>
                </div>
                @endif
            </div>

            {{-- CHAT INPUT AREA --}}
            <div class="p-4 border-t border-gray-200 bg-white">
                <form wire:submit.prevent="sendMessage">
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            wire:model="currentMessage"
                            placeholder="{{ $needsConfirmation ? 'Konfirmasi tiket di atas...' : 'Ketik detail masalah Anda...' }}"
                            class="flex-grow border border-gray-300 rounded-lg p-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black transition">
                        
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="bg-black hover:bg-gray-800 text-white p-2.5 rounded-lg transition-all active:scale-95 disabled:opacity-50">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>