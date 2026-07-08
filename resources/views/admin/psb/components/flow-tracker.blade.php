{{-- VISUAL FLOW TRACKER - Perjalanan Pendaftaran PSB --}}
@props(['applicant'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="text-sm font-bold text-gray-900 mb-6 flex items-center gap-2">
        <i class="fas fa-route text-teal-600"></i> Perjalanan Pendaftaran
    </h3>

    <div class="relative">
        {{-- Decorative Background Circles --}}
        <div class="absolute top-0 left-1/4 w-32 h-32 bg-gradient-to-br from-blue-200 to-purple-200 rounded-full opacity-20 blur-2xl"></div>
        <div class="absolute top-0 right-1/4 w-40 h-40 bg-gradient-to-br from-pink-200 to-orange-200 rounded-full opacity-20 blur-2xl"></div>
        
        {{-- Progress Bar Background --}}
        <div class="absolute top-20 left-0 w-full h-2 bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 rounded-full shadow-inner"></div>
        
        {{-- Active Progress with Gradient & Animation --}}
        @php
            $statuses = ['draft', 'submitted', 'payment_verified', 'document_verified', 'tested', 'scored', 'accepted', 'rejected', 'reregistered', 'registered'];
            $currentIndex = array_search($applicant->status, $statuses);
            $progress = ($currentIndex / (count($statuses) - 1)) * 100;
        @endphp
        <div class="absolute top-20 left-0 h-2 bg-gradient-to-r from-emerald-400 via-teal-500 to-cyan-500 rounded-full transition-all duration-1000 ease-out shadow-lg" 
             style="width: {{ $progress }}%">
            <div class="absolute right-0 top-1/2 transform -translate-y-1/2 w-4 h-4 bg-white rounded-full shadow-lg animate-pulse"></div>
        </div>

        {{-- Steps dengan Design Menarik --}}
        <div class="relative flex justify-between pt-2">
            @php
                $steps = [
                    ['key' => 'draft', 'icon' => '<i class="fas fa-edit"></i>', 'emoji' => '<i class="fas fa-pencil-alt"></i>', 'label' => 'Draft', 'desc' => 'Mulai Mendaftar', 'color' => 'from-gray-400 to-gray-500'],
                    ['key' => 'submitted', 'icon' => '<i class="fas fa-envelope"></i>', 'emoji' => '<i class="fas fa-envelope"></i>', 'label' => 'Disubmit', 'desc' => 'Menunggu Verifikasi', 'color' => 'from-blue-400 to-blue-600'],
                    ['key' => 'payment_verified', 'icon' => '<i class="fas fa-credit-card mr-1"></i>', 'emoji' => '<i class="fas fa-coins mr-1"></i>', 'label' => 'Bayar', 'desc' => 'Pembayaran OK', 'color' => 'from-indigo-400 to-indigo-600'],
                    ['key' => 'document_verified', 'icon' => '<i class="fas fa-file-alt mr-1"></i>', 'emoji' => '<i class="fas fa-clipboard mr-1"></i>', 'label' => 'Dokumen', 'desc' => 'Dokumen OK', 'color' => 'from-purple-400 to-purple-600'],
                    ['key' => 'tested', 'icon' => '<i class="fas fa-edit mr-1"></i>', 'emoji' => '<i class="fas fa-signature mr-1"></i>', 'label' => 'Tes', 'desc' => 'Sudah Tes', 'color' => 'from-pink-400 to-pink-600'],
                    ['key' => 'scored', 'icon' => '<i class="fas fa-chart-bar mr-1"></i>', 'emoji' => '<i class="fas fa-chart-line mr-1"></i>', 'label' => 'Dinilai', 'desc' => 'Sudah Scoring', 'color' => 'from-orange-400 to-orange-600'],
                    ['key' => 'accepted', 'icon' => '<i class="fas fa-party-horn mr-1"></i>', 'emoji' => '<i class="fas fa-gift mr-1"></i>', 'label' => 'Diterima', 'desc' => 'Lolos Seleksi', 'color' => 'from-green-400 to-green-600'],
                    ['key' => 'reregistered', 'icon' => '<i class="fas fa-check-circle text-green-500 mr-1"></i>', 'emoji' => '<i class="fas fa-money-bill mr-1"></i>', 'label' => 'Daftar Ulang', 'desc' => 'Bayar Pangkal', 'color' => 'from-teal-400 to-teal-600'],
                    ['key' => 'registered', 'icon' => '<i class="fas fa-user-graduate"></i>', 'emoji' => '<i class="fas fa-graduation-cap"></i>', 'label' => 'Siswa Aktif', 'desc' => 'Sudah Jadi Siswa', 'color' => 'from-emerald-400 to-emerald-600'],
                ];
            @endphp

            @foreach($steps as $index => $step)
                @php
                    $isPassed = array_search($step['key'], $statuses) <= $currentIndex;
                    $isCurrent = $step['key'] === $applicant->status;
                    $isRejected = $applicant->status === 'rejected' && $step['key'] === 'rejected';
                @endphp

                <div class="flex flex-col items-center relative group z-10" style="animation: fadeInUp {{ $index * 0.1 }}s ease-out;">
                    {{-- Connector Line (before step) --}}
                    @if($index > 0)
                        <div class="absolute -left-1/2 top-5 w-full h-0.5 {{ $isPassed ? 'bg-gradient-to-r from-emerald-300 to-teal-300' : 'bg-gray-200' }} -z-10"></div>
                    @endif

                    {{-- Step Circle dengan 3D Effect --}}
                    <div class="relative transform transition-all duration-300 hover:scale-110">
                        {{-- Outer Glow Ring --}}
                        @if($isCurrent)
                            <div class="absolute inset-0 rounded-full bg-gradient-to-r {{ $step['color'] }} opacity-50 blur-xl animate-pulse"></div>
                        @endif
                        
                        {{-- Main Circle --}}
                        <div class="relative w-16 h-16 rounded-2xl flex flex-col items-center justify-center text-2xl transition-all duration-500 shadow-2xl {{ $isPassed ? 'bg-gradient-to-br ' . $step['color'] . ' text-white transform rotate-0' : 'bg-white text-gray-400 border-2 border-gray-200' }} {{ $isCurrent ? 'ring-4 ring-offset-2 ring-offset-white scale-125 shadow-2xl' : '' }} {{ $isCurrent ? 'ring-' . explode('-', $step['color'])[1] . '-300' : '' }} {{ $isRejected ? 'bg-gradient-to-br from-red-400 to-pink-500 rotate-12' : '' }}">
                            
                            {{-- Icon --}}
                            <div class="{{ $isCurrent ? 'animate-bounce' : '' }}">
                                {!! $isPassed ? $step['emoji'] : $step['icon'] !!}
                            </div>
                            
                            {{-- Checkmark for passed --}}
                            @if($isPassed && !$isCurrent)
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white rounded-full flex items-center justify-center shadow-lg">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Current Indicator Sparkle --}}
                        @if($isCurrent)
                            <div class="absolute -top-2 -right-2 text-yellow-400 text-xl animate-spin"><i class="fas fa-star"></i></div>
                            <div class="absolute -bottom-2 -left-2 text-yellow-400 text-xl animate-ping"><i class="fas fa-star text-yellow-400 mr-1"></i></div>
                        @endif
                    </div>

                    {{-- Label dengan Gradient Text --}}
                    <div class="mt-4 text-center max-w-[80px]">
                        <p class="text-xs font-bold {{ $isPassed ? 'text-transparent bg-clip-text bg-gradient-to-r ' . $step['color'] : 'text-gray-400' }} {{ $isRejected ? 'text-red-600' : '' }}">
                            {{ $step['label'] }}
                        </p>
                        <p class="text-[9px] {{ $isPassed ? 'text-gray-600' : 'text-gray-400' }} mt-1 hidden lg:block leading-tight">
                            {{ $step['desc'] }}
                        </p>
                    </div>

                    {{-- Tooltip dengan Animasi --}}
                    <div class="absolute -bottom-24 left-1/2 transform -translate-x-1/2 bg-gradient-to-br from-gray-900 to-gray-800 text-white text-xs px-4 py-3 rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none whitespace-nowrap z-50 shadow-2xl border border-gray-700">
                        <div class="font-bold text-sm">{!! $step['emoji'] !!} {{ $step['label'] }}</div>
                        <div class="text-gray-300 text-[10px] mt-1">{{ $step['desc'] }}</div>
                        <div class="absolute -top-2 left-1/2 transform -translate-x-1/2 w-4 h-4 bg-gray-900 rotate-45 border-l border-t border-gray-700"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Info Panel dengan Card Colorful --}}
    <div class="mt-8 pt-6 border-t border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 hover:shadow-sm transition-all">
            <p class="text-[10px] text-blue-600 font-semibold uppercase tracking-wide">No. Pendaftaran</p>
            <p class="text-lg font-bold text-blue-700 mt-1">{{ $applicant->registration_number ?? '-' }}</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 border border-purple-100 hover:shadow-sm transition-all">
            <p class="text-[10px] text-purple-600 font-semibold uppercase tracking-wide">Tanggal Submit</p>
            <p class="text-lg font-bold text-purple-700 mt-1">{{ $applicant->submitted_at ? $applicant->submitted_at->format('d M Y') : '-' }}</p>
        </div>
        <div class="bg-orange-50 rounded-xl p-4 border border-orange-100 hover:shadow-sm transition-all">
            <p class="text-[10px] text-orange-600 font-semibold uppercase tracking-wide">Nilai Akhir</p>
            <p class="text-lg font-bold text-orange-700 mt-1">{{ $applicant->final_score ?? '-' }}</p>
        </div>
        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-100 hover:shadow-sm transition-all">
            <p class="text-[10px] text-yellow-600 font-semibold uppercase tracking-wide">Ranking</p>
            <p class="text-lg font-bold {{ $applicant->ranking && $applicant->ranking <= 3 ? 'text-yellow-600' : 'text-yellow-700' }} mt-1">
                @if($applicant->ranking)
                    #{{ $applicant->ranking }}
                    @if($applicant->ranking <= 3)
                        <i class="fas fa-trophy mr-1"></i>
                    @endif
                @else
                    -
                @endif
            </p>
        </div>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
