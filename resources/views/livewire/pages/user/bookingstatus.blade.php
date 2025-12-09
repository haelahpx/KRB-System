{{-- A simple comment like an actual programmer's simple documentation --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="bg-[#0a0a0a] rounded-xl shadow-sm border-2 border-black p-4 md:p-6 mb-4 md:mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            {{-- Title --}}
            <h1 class="text-xl md:text-2xl font-bold text-white text-center lg:text-left whitespace-nowrap">
                Room Booking Status
            </h1>

            {{-- Tabs --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="flex rounded-lg overflow-hidden bg-gray-100 border border-gray-200 w-full lg:w-auto">

                    {{-- Meeting --}}
                    <button wire:click="setMode('meeting')"
                        class="flex-1 lg:flex-none px-3 md:px-4 py-2 text-sm font-medium text-center
                    {{ $mode === 'meeting'
                        ? 'bg-gray-900 text-white cursor-default'
                        : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                        <div class="inline-flex items-center gap-2 justify-center">
                            <x-heroicon-o-building-office-2 class="w-4 h-4" />
                            Meeting
                        </div>
                    </button>

                    {{-- Online --}}
                    <button wire:click="setMode('online')"
                        class="flex-1 lg:flex-none px-3 md:px-4 py-2 text-sm font-medium text-center
                    {{ $mode === 'online'
                        ? 'bg-gray-900 text-white cursor-default'
                        : 'text-gray-700 hover:text-gray-900 hover:bg-gray-50' }}">
                        <div class="inline-flex items-center gap-2 justify-center">
                            <x-heroicon-o-video-camera class="w-4 h-4" />
                            Online
                        </div>
                    </button>

                </div>

            </div>

        </div>

    </div>

    {{-- Main Content --}}
    <div class="bg-white rounded-xl shadow-sm border-2 border-black p-4 md:p-5">
        {{-- Search / Filters header --}}
        <div class="flex flex-col md:flex-row md:items-center gap-4 pb-4 mb-4 border-b border-gray-100">

            {{-- DESKTOP (unchanged) --}}
            <div class="hidden md:grid md:grid-cols-4 gap-3 w-full">

                {{-- Search --}}
                <div class="relative md:col-span-1">
                    <input type="text"
                        wire:model.live.debounce.400ms="q"
                        placeholder="Search title..."
                        class="w-full px-3 py-2 pl-9 text-sm text-gray-900 placeholder:text-gray-400
                border border-gray-300 rounded-md focus:outline-none focus:ring-2
                focus:ring-gray-900 focus:border-transparent">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                </div>

                {{-- Room Filter (Meeting Only) --}}
                @if($mode === 'meeting')
                <div>
                    <select wire:model.live="roomFilter"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md
                focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="">All Rooms</option>
                        @foreach($rooms as $r)
                        <option value="{{ $r->room_id }}">{{ $r->room_number }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Sort --}}
                <div @class([$mode==='meeting' ? '' : 'md:col-span-2' ])>
                    <select wire:model.live="sortFilter"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md
                focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="recent">Newest first</option>
                        <option value="oldest">Oldest first</option>
                        <option value="nearest">Nearest time</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <select wire:model.live="dbStatusFilter"
                        class="w-full px-3 py-2 text-sm text-gray-900 border border-gray-300 rounded-md
                focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                        <option value="all">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            {{-- MOBILE (fixed layout) --}}
            <div class="md:hidden w-full space-y-3">

                {{-- Search full width --}}
                <div class="relative">
                    <input type="text"
                        wire:model.live.debounce.400ms="q"
                        placeholder="Search title..."
                        class="w-full px-3 py-2 pl-9 text-sm text-gray-900 placeholder:text-gray-400
                border border-gray-300 rounded-md focus:outline-none focus:ring-2
                focus:ring-gray-900 focus:border-transparent">
                    <x-heroicon-o-magnifying-glass
                        class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                </div>

                {{-- Filters Row --}}
                <div class="@if($mode === 'meeting') grid grid-cols-3 gap-3 @else grid grid-cols-2 gap-3 @endif">

                    {{-- Room Filter (Meeting Only) --}}
                    @if($mode === 'meeting')
                    <select wire:model.live="roomFilter"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-md w-full">
                        <option value="">Rooms</option>
                        @foreach($rooms as $r)
                        <option value="{{ $r->room_id }}">{{ $r->room_number }}</option>
                        @endforeach
                    </select>
                    @endif

                    {{-- Sort --}}
                    <select wire:model.live="sortFilter"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-md w-full">
                        <option value="recent">Newest</option>
                        <option value="oldest">Oldest</option>
                        <option value="nearest">Nearest</option>
                    </select>

                    {{-- Status --}}
                    <select wire:model.live="dbStatusFilter"
                        class="px-3 py-2 text-sm border border-gray-300 rounded-md w-full">
                        <option value="all">Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="completed">Completed</option>
                    </select>

                </div>
            </div>

        </div>

        {{-- List --}}
        <div class="space-y-4">
            @forelse($bookings as $b)
            @php
            $start = \Carbon\Carbon::parse($b->start_time, 'Asia/Jakarta');
            $end = \Carbon\Carbon::parse($b->end_time, 'Asia/Jakarta');
            $dateStr = $start->format('D, M j, Y');
            $timeStr = $start->format('H:i').'–'.$end->format('H:i');
            $roomName = $roomMap[$b->room_id] ?? 'Unknown';

            // Standardized Badge Classes
            $statusConfig = match($b->status) {
            'pending' => ['class' => 'bg-yellow-100 text-yellow-800 border-yellow-200', 'icon' => 'clock'],
            'approved' => ['class' => 'bg-green-100 text-green-800 border-green-200', 'icon' => 'check-circle'],
            'rejected' => ['class' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'x-circle'],
            'completed' => ['class' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'archive-box'],
            default => ['class' => 'bg-gray-50 text-gray-600 border-gray-200', 'icon' => 'question-mark-circle'],
            };
            @endphp

            <div class="group relative bg-white rounded-xl border-2 border-black p-5 hover:shadow-md transition-shadow duration-200">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">
                            {{ $b->meeting_title ?? 'Untitled' }}
                        </h3>

                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-mono font-medium text-gray-800 inline-flex items-center gap-1 bg-gray-100 px-2 py-1 rounded">
                                <x-heroicon-o-hashtag class="w-3 h-3" /> {{ $b->bookingroom_id }}
                            </span>

                            @if($mode === 'meeting' && $b->room_id)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 font-medium">
                                <x-heroicon-o-building-office-2 class="w-3 h-3" />
                                Room {{ $roomName }}
                            </span>
                            @endif

                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 font-medium">
                                <x-heroicon-o-calendar-days class="w-3 h-3" />
                                {{ $dateStr }}
                            </span>

                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-gray-200 bg-gray-50 text-gray-700 font-medium">
                                <x-heroicon-o-clock class="w-3 h-3" />
                                {{ $timeStr }}
                            </span>

                            @if($mode === 'online' && !empty($b->online_provider))
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-blue-200 bg-blue-50 text-blue-700 font-medium">
                                <x-heroicon-o-video-camera class="w-3 h-3" />
                                {{ ucfirst($b->online_provider) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border {{ $statusConfig['class'] }}">
                        @if($statusConfig['icon'] === 'clock') <x-heroicon-o-clock class="w-3.5 h-3.5" />
                        @elseif($statusConfig['icon'] === 'check-circle') <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                        @elseif($statusConfig['icon'] === 'x-circle') <x-heroicon-o-x-circle class="w-3.5 h-3.5" />
                        @else <x-heroicon-o-archive-box class="w-3.5 h-3.5" />
                        @endif
                        {{ ucfirst($b->status) }}
                    </span>
                </div>

                @if(!empty($b->special_notes))
                <div class="mt-3 text-sm text-gray-600 bg-gray-50 rounded-lg p-3 border border-gray-100">
                    <span class="font-semibold text-gray-900">Note:</span> {{ $b->special_notes }}
                </div>
                @endif

                {{-- Rejection / Notes --}}
                @if($b->status === 'rejected' && !empty($b->book_reject))
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3">
                    <div class="text-xs font-bold text-red-800 inline-flex items-center gap-1 mb-1">
                        <x-heroicon-o-exclamation-circle class="w-4 h-4" />
                        Rejection Reason
                    </div>
                    <div class="text-sm text-red-700">{{ $b->book_reject }}</div>
                </div>
                @elseif(!empty($b->book_reject))
                <div class="mt-3 rounded-lg border border-yellow-200 bg-yellow-50 p-3">
                    <div class="text-xs font-bold text-yellow-800 inline-flex items-center gap-1 mb-1">
                        <x-heroicon-o-information-circle class="w-4 h-4" />
                        Note
                    </div>
                    <div class="text-sm text-yellow-700">{{ $b->book_reject }}</div>
                </div>
                @endif

                {{-- Online Details --}}
                @if($mode === 'online' && $b->status === 'approved')
                <div class="mt-4 pt-3 border-t border-dashed border-gray-200 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-2 text-xs">
                    @if(!empty($b->online_meeting_url))
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-medium">Meeting Link</span>
                        <a href="{{ $b->online_meeting_url }}" target="_blank" class="text-blue-600 hover:underline truncate font-medium flex items-center gap-1">
                            {{ $b->online_meeting_url }}
                            <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3" />
                        </a>
                    </div>
                    @endif
                    @if(!empty($b->online_meeting_code))
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-medium">Meeting ID / Code</span>
                        <span class="text-gray-900 font-mono">{{ $b->online_meeting_code }}</span>
                    </div>
                    @endif
                    @if(!empty($b->online_meeting_password))
                    <div class="flex flex-col">
                        <span class="text-gray-500 font-medium">Password</span>
                        <span class="text-gray-900 font-mono">{{ $b->online_meeting_password }}</span>
                    </div>
                    @endif
                </div>
                @endif

                <div class="mt-4 flex items-center justify-between text-[10px] text-gray-400 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-1">
                            <x-heroicon-o-pencil-square class="w-3 h-3" />
                            Created {{ optional($b->created_at)->format('d M Y') }}
                        </div>
                        @if($b->updated_at != $b->created_at)
                        <div class="flex items-center gap-1">
                            <x-heroicon-o-arrow-path class="w-3 h-3" />
                            Updated {{ optional($b->updated_at)->diffForHumans() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-xl border-2 border-dashed border-gray-300 p-12 text-center">
                {{-- Original SVG was a Calendar/Date icon --}}
                <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting the filters above.</p>
            </div>
            @endforelse
        </div>

        @if(method_exists($bookings, 'links'))
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>
</div>