@if(isset($recentAlumnis) && $recentAlumnis->count() > 0)
<section class="py-20 relative overflow-hidden bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-extrabold text-indigo-900 mb-4 inline-block relative">
                Suara Alumni PEMBDA Nias
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-16 h-1.5 bg-gold rounded-full"></div>
            </h2>
            <p class="text-slate-600 max-w-2xl mx-auto mt-4 text-lg">
                Lintas generasi, merajut kembali kisah dan kenangan indah. Inilah cerita dan dukungan dari mereka yang pernah mengenyam pendidikan di almamater tercinta.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($recentAlumnis as $alumni)
            <div class="glass-card overflow-hidden rounded-3xl hover:transform hover:scale-105 transition-all duration-300 relative group flex flex-col" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}" style="background: white; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);">
                
                <div class="aspect-[4/3] w-full relative">
                    <img src="{{ $alumni->photo_url }}" class="w-full h-full object-cover" alt="{{ $alumni->full_name }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-indigo-900/90 via-indigo-900/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                        <h4 class="font-bold text-lg leading-tight">{{ $alumni->full_name }}</h4>
                        <p class="text-xs text-indigo-200 mt-1"><i class="fas fa-graduation-cap text-gold mr-1"></i>Lulusan {{ $alumni->school->name ?? 'PEMBDA' }} '{{ $alumni->graduation_year }}</p>
                    </div>
                    <i class="fa-solid fa-quote-right absolute top-4 right-4 text-3xl text-white/30 group-hover:text-gold transition-colors"></i>
                </div>
                
                <div class="p-6 flex flex-col flex-grow">
                    @php
                        $alias = $alumni->alias_name ? "sekarang dikenal dengan nama <strong>{$alumni->alias_name}</strong>, " : "";
                        $anak = $alumni->children_count ? "dengan {$alumni->children_count} orang anak, " : "";
                        $status = $alumni->marital_status ? "berstatus {$alumni->marital_status}, " : "";
                        
                        $kerja = "";
                        if($alumni->occupation && $alumni->company_name) {
                            $kerja = "sehari-hari bekerja sebagai {$alumni->occupation} di {$alumni->company_name}";
                        } elseif($alumni->occupation) {
                            $kerja = "sehari-hari bekerja sebagai {$alumni->occupation}";
                        } elseif($alumni->company_name) {
                            $kerja = "sehari-hari bekerja di {$alumni->company_name}";
                        }
                        
                        $narrative = trim("{$alias} {$status} {$anak} {$kerja}", ", ");
                    @endphp
                    
                    <div class="text-sm text-slate-600 italic flex-grow mb-4 relative z-10">
                        "{{ $alumni->message }}"
                    </div>
                    
                    @if($narrative)
                    <div class="text-[11px] text-slate-500 mt-auto bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                        <strong>{{ $alumni->full_name }}</strong> {!! $narrative !!}.
                    </div>
                    @endif
                </div>
                
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12" data-aos="fade-up">
            <a href="{{ route('ika.register') }}" class="inline-flex items-center gap-2 bg-indigo-900 text-white font-bold py-3 px-8 rounded-full hover:bg-indigo-800 transition shadow-lg hover:shadow-indigo-900/50">
                <i class="fa-solid fa-users"></i> Ikut Rembuk Alumni Sekarang
            </a>
        </div>
        
    </div>
</section>
@endif
