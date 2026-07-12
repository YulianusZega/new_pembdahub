@extends('layouts.alumni')

@section('title', 'Pesan Pribadi')

@section('content')
<div class="max-w-6xl mx-auto h-[calc(100vh-8rem)] min-h-[500px]">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm h-full flex overflow-hidden">
        
        <!-- Sidebar Daftar Kontak (Alumni) -->
        <div class="w-full sm:w-80 bg-gray-50 border-r border-gray-200 flex flex-col shrink-0 {{ isset($contact) ? 'hidden sm:flex' : 'flex' }}">
            <div class="p-4 border-b border-gray-200 bg-white">
                <h2 class="font-bold text-gray-800 text-lg">Direktori Alumni</h2>
                <p class="text-xs text-gray-500 mt-1">Hanya alumni unit sekolah Anda</p>
                
                <div class="mt-4 relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" placeholder="Cari nama alumni..." class="w-full pl-9 pr-4 py-2 bg-gray-100 border-transparent rounded-xl text-sm focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                @forelse($alumnis as $al)
                    <a href="{{ route('alumni.chat.show', $al->id) }}" class="flex items-center gap-3 p-3 rounded-xl transition {{ isset($contact) && $contact->id === $al->id ? 'bg-indigo-50 border border-indigo-100' : 'hover:bg-white border border-transparent' }}">
                        <div class="relative shrink-0">
                            <img src="{{ $al->photo_url }}" class="w-12 h-12 rounded-full object-cover shadow-sm">
                            <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-sm text-gray-900 truncate">{{ $al->name }}</h4>
                            <p class="text-[11px] text-gray-500 truncate">Alumni &bull; Tahun {{ $al->alumniDirectory->graduation_year ?? '-' }}</p>
                        </div>
                    </a>
                @empty
                    <div class="text-center p-6">
                        <p class="text-xs text-gray-500">Belum ada alumni lain yang mendaftar dari unit sekolah Anda.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Area Chat -->
        <div class="flex-1 flex flex-col bg-white {{ !isset($contact) ? 'hidden sm:flex' : 'flex' }}">
            @if(isset($contact))
                <!-- Header Chat -->
                <div class="px-6 py-4 border-b border-gray-200 bg-white flex items-center gap-4">
                    <a href="{{ route('alumni.chat.index') }}" class="sm:hidden w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <img src="{{ $contact->photo_url }}" class="w-10 h-10 rounded-full object-cover shadow-sm">
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">{{ $contact->name }}</h3>
                        <p class="text-[11px] text-gray-500">Lulusan Tahun {{ $contact->alumniDirectory->graduation_year ?? '-' }}</p>
                    </div>
                </div>

                <!-- Bubble Chat List -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50" id="chatContainer">
                    @forelse($messages as $msg)
                        @if($msg->sender_id === auth()->id())
                            <!-- Pesan Saya (Kanan) -->
                            <div class="flex justify-end">
                                <div class="max-w-[75%]">
                                    <div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tr-sm shadow-sm text-sm">
                                        {{ $msg->message }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-1 text-right">
                                        {{ $msg->created_at->format('H:i') }}
                                        @if($msg->is_read)
                                            <i class="fas fa-check-double text-blue-500 ml-1"></i>
                                        @else
                                            <i class="fas fa-check text-gray-300 ml-1"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Pesan Dia (Kiri) -->
                            <div class="flex justify-start">
                                <div class="max-w-[75%] flex gap-3 items-end">
                                    <img src="{{ $contact->photo_url }}" class="w-6 h-6 rounded-full object-cover shrink-0 mb-5">
                                    <div>
                                        <div class="bg-white border border-gray-200 text-gray-800 p-3 rounded-2xl rounded-tl-sm shadow-sm text-sm">
                                            {{ $msg->message }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-1">
                                            {{ $msg->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-hand-sparkles text-2xl text-gray-400"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-700">Mulai Percakapan Baru</h4>
                            <p class="text-xs text-gray-500 mt-1">Sapa teman lama Anda ini untuk membuka obrolan.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Input Chat -->
                <div class="p-4 bg-white border-t border-gray-200">
                    <form action="{{ route('alumni.chat.store', $contact->id) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="message" required placeholder="Tulis pesan..." autocomplete="off" class="flex-1 bg-gray-100 border-transparent focus:bg-white focus:border-indigo-500 rounded-full px-5 py-3 text-sm">
                        <button type="submit" class="w-12 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full flex items-center justify-center shadow-md transition shrink-0">
                            <i class="fas fa-paper-plane text-sm ml-[-2px]"></i>
                        </button>
                    </form>
                </div>
                
                <script>
                    // Scroll to bottom automatically
                    var chatContainer = document.getElementById('chatContainer');
                    if (chatContainer) {
                        chatContainer.scrollTop = chatContainer.scrollHeight;
                    }
                </script>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center p-6 bg-slate-50">
                    <img src="https://illustrations.popsy.co/amber/communication.svg" class="w-48 h-48 opacity-50 mb-4" alt="Chat Empty">
                    <h2 class="text-xl font-bold text-gray-800">Pesan Pribadi Alumni</h2>
                    <p class="text-sm text-gray-500 mt-2 max-w-sm">Pilih alumni dari direktori di sebelah kiri untuk mulai mengobrol, bernostalgia, atau berdiskusi secara privat.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
