{{-- Reusable Document & Attachment Preview Modal (Alpine.js) --}}
<div x-data="{
    show: false,
    fileUrl: '',
    fileName: '',
    fileType: '',
    isLoading: false,
    open(url, name) {
        if (!url) return;
        this.fileUrl = url;
        this.fileName = name || 'Dokumen Lampiran';
        this.isLoading = true;
        let ext = url.split('.').pop().toLowerCase();
        if (ext.includes('?')) ext = ext.split('?')[0];
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'].includes(ext)) {
            this.fileType = 'image';
        } else if (ext === 'pdf') {
            this.fileType = 'pdf';
        } else if (['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'].includes(ext)) {
            this.fileType = 'office';
        } else {
            this.fileType = 'other';
        }
        this.show = true;
    },
    close() {
        this.show = false;
        setTimeout(() => { this.fileUrl = ''; }, 200);
    }
}"
@open-preview-modal.window="open($event.detail.url, $event.detail.name)"
@keydown.escape.window="close()"
class="relative z-[9999]">
    {{-- Backdrop --}}
    <div x-show="show" x-cloak 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm transition-opacity" 
         @click="close()"></div>

    {{-- Modal Container --}}
    <div x-show="show" x-cloak class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-3 sm:p-6 text-center">
            <div x-show="show" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="w-full max-w-5xl transform overflow-hidden rounded-3xl bg-white text-left align-middle shadow-2xl transition-all border border-gray-200 flex flex-col max-h-[92vh]" 
                 @click.stop>
                
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 bg-slate-900 text-white rounded-t-3xl">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-slate-800 flex items-center justify-center text-emerald-400 flex-shrink-0">
                            <i class="fas fa-file-pdf text-lg" x-show="fileType === 'pdf'"></i>
                            <i class="fas fa-image text-lg" x-show="fileType === 'image'"></i>
                            <i class="fas fa-file-word text-lg" x-show="fileType === 'office'"></i>
                            <i class="fas fa-file-alt text-lg" x-show="fileType === 'other'"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-sm sm:text-base font-black tracking-wide text-white truncate" x-text="fileName"></h3>
                            <p class="text-[11px] text-slate-400 font-medium">Pratinjau Dokumen & Lampiran Langsung di Layar</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a :href="fileUrl" target="_blank" download class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-700 shadow-sm" title="Unduh File Asli">
                            <i class="fas fa-download"></i> <span class="hidden sm:inline">Unduh File</span>
                        </a>
                        <button type="button" @click="close()" class="w-9 h-9 rounded-xl bg-slate-800 hover:bg-rose-600 text-slate-300 hover:text-white transition flex items-center justify-center font-bold" title="Tutup Preview">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-4 sm:p-6 overflow-auto flex-1 flex items-center justify-center bg-slate-100 min-h-[65vh] md:min-h-[75vh] relative">
                    {{-- Loading spinner --}}
                    <div x-show="isLoading" class="absolute inset-0 flex items-center justify-center bg-slate-100 z-10">
                        <div class="flex flex-col items-center gap-3">
                            <i class="fas fa-spinner fa-spin text-4xl text-emerald-600"></i>
                            <p class="text-xs font-extrabold text-slate-600 animate-pulse">Memuat pratinjau dokumen...</p>
                        </div>
                    </div>

                    {{-- Image Preview --}}
                    <template x-if="fileType === 'image'">
                        <div class="max-w-full max-h-full flex items-center justify-center p-2">
                            <img :src="fileUrl" @load="isLoading = false" @error="isLoading = false" class="max-h-[72vh] w-auto object-contain rounded-xl shadow-lg border border-gray-300 bg-white" alt="Preview Image">
                        </div>
                    </template>

                    {{-- PDF Preview --}}
                    <template x-if="fileType === 'pdf'">
                        <iframe :src="fileUrl" @load="isLoading = false" class="w-full h-[75vh] rounded-xl border border-gray-300 shadow-md bg-white"></iframe>
                    </template>

                    {{-- Office Preview (DOCX, XLSX, PPTX) --}}
                    <template x-if="fileType === 'office'">
                        <iframe :src="'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(fileUrl)" @load="isLoading = false" class="w-full h-[75vh] rounded-xl border border-gray-300 shadow-md bg-white"></iframe>
                    </template>

                    {{-- Other File Types --}}
                    <template x-if="fileType === 'other'">
                        <div class="text-center p-8 bg-white rounded-2xl shadow-sm border border-gray-200 max-w-md">
                            <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center mx-auto mb-4 text-2xl">
                                <i class="fas fa-file-archive"></i>
                            </div>
                            <h4 class="font-extrabold text-slate-800 text-base mb-1">Format Tidak Dapat Dipreview Secara Langsung</h4>
                            <p class="text-xs text-slate-600 mb-6 font-medium leading-relaxed">Format file ini membutuhkan aplikasi khusus untuk dibuka. Silakan unduh dokumen untuk membaca dan memberikan feedback.</p>
                            <a :href="fileUrl" target="_blank" download class="px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition inline-flex items-center gap-2">
                                <i class="fas fa-download"></i> Unduh Dokumen Sekarang
                            </a>
                        </div>
                    </template>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-3.5 bg-gray-50 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500 font-medium">
                    <span><i class="fas fa-info-circle text-emerald-600 mr-1"></i> Gunakan pratinjau ini untuk mempercepat pemeriksaan sebelum memberi masukan.</span>
                    <button type="button" @click="close()" class="px-4 py-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold transition">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
