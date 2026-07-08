<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Gabung Live Game - PembdaHUB</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; color: white; font-family: 'Inter', sans-serif; overflow: hidden; }
        .bg-gradient { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
        input { text-align: center; font-weight: 900 !important; }
        .btn-xl { font-size: 1.25rem; font-weight: 800; border-radius: 1rem; cursor: pointer; transition: all 0.2s; }
        .btn-xl:active { transform: scale(0.95); }
    </style>
</head>
<body class="bg-gradient w-screen h-screen flex flex-col items-center justify-center p-4">
    
    <div class="mb-8 text-center">
        <div class="w-20 h-20 bg-cyan-500 rounded-2xl mx-auto flex items-center justify-center shadow-[0_0_30px_rgba(6,182,212,0.5)] mb-4">
            <i class="fas fa-gamepad text-white text-4xl"></i>
        </div>
        <h1 class="text-3xl font-black tracking-tight">PembdaHUB Live</h1>
    </div>

    <form action="{{ route('live.processJoin') }}" method="POST" class="w-full max-w-sm flex flex-col gap-4">
        @csrf
        
        @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 text-red-100 p-4 rounded-xl text-center font-bold text-sm">
            {{ session('error') }}
        </div>
        @endif

        <input type="number" name="pin" required placeholder="PIN GAME" class="w-full bg-white text-slate-900 rounded-xl p-4 text-2xl uppercase focus:outline-none focus:ring-4 focus:ring-cyan-500" value="{{ old('pin') }}">
        
        <input type="text" name="nickname" required placeholder="NAMA PANGGILAN" class="w-full bg-white text-slate-900 rounded-xl p-4 text-xl focus:outline-none focus:ring-4 focus:ring-cyan-500" value="{{ old('nickname', $defaultName ?? '') }}">
        
        <button type="submit" class="btn-xl w-full bg-cyan-600 hover:bg-cyan-500 text-white p-4 shadow-[0_4px_15px_rgba(8,145,178,0.4)] mt-4">
            Masuk <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </form>

</body>
</html>
