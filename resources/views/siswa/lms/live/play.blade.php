<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bermain Live - PembdaHUB</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>
        body { background-color: #f1f5f9; font-family: 'Inter', sans-serif; overflow: hidden; touch-action: manipulation; }
        .color-btn { 
            transition: transform 0.1s; 
            border-bottom: 8px solid rgba(0,0,0,0.2); 
            cursor: pointer;
        }
        .color-btn:active { transform: translateY(4px); border-bottom-width: 4px; }
        .btn-red { background-color: #ef4444; }
        .btn-blue { background-color: #3b82f6; }
        .btn-yellow { background-color: #eab308; }
        .btn-green { background-color: #22c55e; }
        
        .loading-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        .score-pop { animation: pop 0.5s ease-out; }
        @keyframes pop {
            0% { transform: scale(0.5); opacity: 0; }
            70% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="w-screen h-screen flex flex-col" x-data="playerGame()">

    <!-- HEADER -->
    <div class="w-full bg-white shadow-sm p-4 flex justify-between items-center z-50">
        <div class="font-bold text-slate-800 flex items-center gap-2">
            <i class="fas fa-user-circle text-slate-400 text-xl"></i>
            {{ $player->nickname }}
        </div>
        <div class="bg-slate-100 px-3 py-1 rounded-md font-mono font-bold text-sm text-slate-600">
            PIN: {{ $session->pin_code }}
        </div>
        <div class="font-black text-slate-800">
            <span x-text="state.player_score"></span>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 w-full relative">

        <!-- WAITING -->
        <div x-show="state.status === 'waiting' || (state.status === 'question' && state.has_answered) || state.status === 'leaderboard'" class="absolute inset-0 bg-slate-100 flex flex-col items-center justify-center p-6 text-center z-40">
            
            <template x-if="state.status === 'waiting'">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 mb-2">Kamu sudah masuk!</h2>
                    <p class="text-slate-500 font-bold mb-8">Lihat ke layar proyektor. Tunggu guru memulai permainan...</p>
                    <div class="w-16 h-16 border-4 border-cyan-500 border-t-transparent rounded-full animate-spin mx-auto"></div>
                </div>
            </template>

            <template x-if="state.status === 'question' && state.has_answered">
                <div>
                    <div class="w-24 h-24 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-4xl text-slate-400"></i>
                    </div>
                    <h2 class="text-2xl font-black text-slate-800 mb-2">Jawaban Tersimpan!</h2>
                    <p class="text-slate-500 font-bold mb-8">Tunggu waktu habis untuk melihat hasilnya...</p>
                    <div class="loading-pulse">Tunggu sebentar...</div>
                </div>
            </template>

            <template x-if="state.status === 'leaderboard'">
                <div class="w-full flex flex-col items-center">
                    <!-- Incorrect / Correct Feedback -->
                    <template x-if="state.my_answer && state.my_answer.is_correct">
                        <div class="score-pop w-full">
                            <div class="w-24 h-24 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-[0_0_20px_rgba(34,197,94,0.5)]">
                                <i class="fas fa-check text-5xl text-white"></i>
                            </div>
                            <h2 class="text-3xl font-black text-green-600 mb-2">BENAR!</h2>
                            <div class="bg-green-100 text-green-800 px-6 py-2 rounded-full font-bold text-xl inline-block mb-8 border border-green-200">
                                +<span x-text="state.my_answer.points"></span> Poin
                            </div>
                        </div>
                    </template>
                    <template x-if="state.my_answer && !state.my_answer.is_correct">
                        <div class="score-pop w-full">
                            <div class="w-24 h-24 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-[0_0_20px_rgba(239,68,68,0.5)]">
                                <i class="fas fa-times text-5xl text-white"></i>
                            </div>
                            <h2 class="text-3xl font-black text-red-600 mb-2">SALAH!</h2>
                            <p class="text-red-400 font-bold mb-8">Jangan menyerah!</p>
                        </div>
                    </template>
                    <template x-if="!state.my_answer">
                        <div class="w-full">
                            <div class="w-24 h-24 bg-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-clock text-5xl text-slate-500"></i>
                            </div>
                            <h2 class="text-2xl font-black text-slate-600 mb-2">Waktu Habis!</h2>
                            <p class="text-slate-500 font-bold mb-8">Kamu tidak menjawab tepat waktu.</p>
                        </div>
                    </template>

                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm w-full max-w-xs">
                        <p class="text-slate-500 text-sm font-bold uppercase mb-1">Peringkat Kamu</p>
                        <div class="text-3xl font-black text-slate-800">
                            Ke-<span x-text="state.rank"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- CONTROLLER (QUESTION ACTIVE) -->
        <div x-show="state.status === 'question' && !state.has_answered" class="absolute inset-0 bg-slate-800 flex flex-col p-2 z-30" style="display: none;">
            
            <template x-if="gameType === 'quiz'">
                <div class="flex-1 grid grid-cols-2 grid-rows-2 gap-2">
                    <div @click="submitAnswer(0)" class="color-btn btn-red rounded-xl w-full h-full"></div>
                    <div @click="submitAnswer(1)" class="color-btn btn-blue rounded-xl w-full h-full"></div>
                    <div @click="submitAnswer(2)" class="color-btn btn-yellow rounded-xl w-full h-full"></div>
                    <div @click="submitAnswer(3)" class="color-btn btn-green rounded-xl w-full h-full"></div>
                </div>
            </template>
            
            <template x-if="gameType === 'true_false'">
                <div class="flex-1 grid grid-cols-1 grid-rows-2 gap-2">
                    <div @click="submitAnswer('true')" class="color-btn btn-blue rounded-xl w-full h-full flex items-center justify-center">
                        <span class="text-4xl font-black text-white">BENAR</span>
                    </div>
                    <div @click="submitAnswer('false')" class="color-btn btn-red rounded-xl w-full h-full flex items-center justify-center">
                        <span class="text-4xl font-black text-white">SALAH</span>
                    </div>
                </div>
            </template>
        </div>

        <!-- FINISHED -->
        <div x-show="state.status === 'finished'" class="absolute inset-0 bg-slate-900 flex flex-col items-center justify-center p-6 text-center z-50 text-white" style="display: none;">
            <i class="fas fa-flag-checkered text-6xl text-yellow-400 mb-6"></i>
            <h1 class="text-4xl font-black mb-2">Permainan Selesai!</h1>
            <p class="text-xl text-slate-300 mb-8">Lihat layar proyektor untuk mengetahui pemenangnya.</p>
            
            <div class="bg-white/10 p-6 rounded-2xl border border-white/20 w-full max-w-xs mb-8">
                <p class="text-slate-400 text-sm font-bold uppercase mb-1">Skor Akhir</p>
                <div class="text-4xl font-black text-yellow-400 mb-4" x-text="state.player_score"></div>
                <p class="text-slate-400 text-sm font-bold uppercase mb-1">Peringkat Akhir</p>
                <div class="text-3xl font-black">Ke-<span x-text="state.rank"></span></div>
            </div>

            <a href="{{ route('home') }}" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 rounded-xl font-bold transition">Tutup</a>
        </div>

    </div>

    <script>
        const sessionId = {{ $session->id }};
        const csrfToken = '{{ csrf_token() }}';

        document.addEventListener('alpine:init', () => {
            Alpine.data('playerGame', () => ({
                gameType: '{{ $session->game->game_type }}',
                state: {
                    status: '{{ $session->status }}',
                    current_question_index: {{ $session->current_question_index }},
                    has_answered: false,
                    my_answer: null,
                    player_score: {{ $player->score }},
                    rank: 0
                },
                pollInterval: null,
                isSubmitting: false,

                init() {
                    this.startPolling();
                },

                startPolling() {
                    this.pollInterval = setInterval(async () => {
                        try {
                            const res = await fetch(`/live/${sessionId}/poll`);
                            const data = await res.json();
                            
                            // Reset state if moving to a new question
                            if (this.state.status === 'leaderboard' && data.status === 'question' && data.current_question_index > this.state.current_question_index) {
                                this.state.has_answered = false;
                                this.state.my_answer = null;
                            }

                            this.state = data;
                        } catch(e) { console.error("Polling error", e); }
                    }, 1500); // Poll every 1.5s for fast response
                },

                async submitAnswer(answerValue) {
                    if (this.isSubmitting || this.state.has_answered) return;
                    this.isSubmitting = true;

                    // Optimistically set UI to waiting
                    this.state.has_answered = true;

                    try {
                        const res = await fetch(`/live/${sessionId}/answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ answer: answerValue })
                        });
                        if (!res.ok) {
                            console.error("Failed to submit");
                            this.state.has_answered = false;
                        }
                    } catch(e) {
                        console.error(e);
                        this.state.has_answered = false;
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</body>
</html>
