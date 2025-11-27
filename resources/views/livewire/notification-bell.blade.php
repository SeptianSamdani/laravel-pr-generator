<div class="relative" wire:poll.10s>
    
    {{-- Bell Button --}}
    <button wire:click="$toggle('dropdownOpen')" class="relative p-2 rounded-lg hover:bg-orange-50 text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>

        @if ($unreadCount > 0)
        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-primary-500 rounded-full ring-2 ring-white"></span>
        @endif
    </button>

    {{-- Dropdown --}}
    @if ($dropdownOpen)
    <div class="absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50 p-2">
        
        <h3 class="text-sm font-semibold px-2 py-2 text-gray-700 border-b">Notifikasi</h3>

        @forelse ($notifications as $notif)
            <div class="p-2 hover:bg-orange-50 rounded-md cursor-pointer"
                wire:click="markAsRead('{{ $notif->id }}')">

                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-800 font-medium">
                        {{ $notif->data['message'] ?? 'Notifikasi' }}
                    </p>

                    @if(is_null($notif->read_at))
                        <span class="w-2 h-2 bg-primary-500 rounded-full"></span>
                    @endif
                </div>

                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $notif->created_at->diffForHumans() }}
                </p>
            </div>
        @empty
            <p class="p-4 text-center text-sm text-gray-500">Tidak ada notifikasi</p>
        @endforelse

    </div>
    @endif

</div>
