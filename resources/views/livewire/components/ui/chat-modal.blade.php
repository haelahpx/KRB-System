<div
    x-data="{ 
        show: @entangle('isOpen'),
        isFull: false 
    }"
    x-show="show"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed inset-0 z-[60] flex items-end justify-end"
    role="dialog"
    aria-modal="true"
    style="display: none;">

    <div
        x-on:click="show = false"
        class="fixed inset-0 bg-black/20 backdrop-blur-sm transition-opacity">
    </div>

    <div
        :class="isFull ? 'fixed inset-0 w-full h-full z-[70]' : 'fixed bottom-20 right-4 left-4 sm:left-auto sm:bottom-[4.5rem] sm:right-6 lg:w-[400px] sm:w-[200px] h-[70vh] max-h-[600px] flex flex-col z-[70]'"
        class="transition-all duration-300 ease-in-out">

        <div 
            :class="isFull ? 'rounded-none' : 'rounded-2xl border border-gray-200'"
            class="relative transform overflow-hidden bg-white text-left w-full h-full flex flex-col shadow-2xl">

            <div 
                class="flex items-center justify-between p-4 bg-black text-white shadow-md flex-shrink-0">
                <h3 class="text-sm sm:text-lg font-semibold leading-6 truncate pr-4">
                    Kebun Raya AI Assistant
                </h3>
                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        x-on:click="isFull = !isFull"
                        class="text-gray-300 hover:text-white transition">
                        <template x-if="!isFull">
                            <x-heroicon-o-arrows-pointing-out class="h-5 w-5" />
                        </template>
                        <template x-if="isFull">
                            <x-heroicon-o-arrows-pointing-in class="h-5 w-5" />
                        </template>
                    </button>

                    <button
                        type="button"
                        x-on:click="show = false"
                        class="text-gray-300 hover:text-white transition">
                        <x-heroicon-o-x-mark class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <div
                x-init="Livewire.on('chatUpdated', () => { $nextTick(() => { $el.scrollTop = $el.scrollHeight; }); })"
                class="flex-grow p-4 overflow-y-auto bg-gray-50 space-y-4">
                
                @foreach ($messages as $index => $msg)
                    @if ($index === 0) @continue @endif

                    <div class="flex @if ($msg['role'] === 'user') justify-end @endif">
                        <div class="p-3 rounded-2xl max-w-[85%] text-sm @if ($msg['role'] === 'user') bg-black text-white rounded-tr-none @else bg-white border border-gray-200 text-gray-800 shadow-sm rounded-tl-none @endif">
                            <p class="leading-relaxed">{!! nl2br(e($msg['text'])) !!}</p>
                        </div>
                    </div>
                @endforeach

                <div wire:loading wire:target="generateAiResponse">
                    <div class="flex justify-start">
                        <div class="bg-gray-200 p-3 rounded-2xl rounded-tl-none flex items-center space-x-2">
                            <div class="flex space-x-1">
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce"></div>
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($needsConfirmation)
                <div class="p-4 bg-white border border-gray-300 rounded-xl space-y-3 my-2 shadow-md">
                    <div class="flex items-center gap-2 border-b pb-2">
                        <x-heroicon-o-ticket class="h-5 w-5 text-black" />
                        <h4 class="font-bold text-xs uppercase">Konfirmasi Tiket</h4>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[10px] font-semibold">
                        <div class="bg-gray-50 p-2 rounded border">
                            <p class="text-gray-400">Dept</p>
                            <p class="text-black truncate">
                                {{ collect($availableDepartments)->firstWhere('department_id', $selectedDepartmentId)['department_name'] ?? '-' }}
                            </p>
                        </div>
                        <div class="bg-gray-50 p-2 rounded border">
                            <p class="text-gray-400">Prioritas</p>
                            <p class="{{ $ticketPriority === 'high' ? 'text-red-600' : 'text-blue-600' }}">
                                {{ strtoupper($ticketPriority) }}
                            </p>
                        </div>
                    </div>

                    <p class="text-[11px] italic text-gray-500">"{{ $aiReasoning }}"</p>

                    <div class="space-y-1">
                        <select wire:model="selectedDepartmentId" class="w-full text-xs border-gray-200 rounded-lg">
                            @foreach ($availableDepartments as $dept)
                                <option value="{{ $dept['department_id'] }}">{{ $dept['department_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="confirmTicket" class="flex-1 bg-black text-white py-2 rounded-lg text-xs font-bold active:scale-95 transition">
                            Buat Tiket
                        </button>
                        <button wire:click="cancelTicket" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold">
                            Batal
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <div class="p-4 border-t border-gray-200 bg-white flex-shrink-0">
                <form wire:submit.prevent="sendMessage">
                    <div class="flex items-center gap-2">
                        <input
                            type="text"
                            wire:model="currentMessage"
                            placeholder="Ketik detail masalah..."
                            class="flex-grow border border-gray-300 rounded-xl p-2.5 text-sm focus:ring-1 focus:ring-black focus:border-black transition">
                        
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="bg-black text-white p-2.5 rounded-xl flex-shrink-0 active:scale-90 transition">
                            <x-heroicon-o-paper-airplane class="h-5 w-5" />
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>