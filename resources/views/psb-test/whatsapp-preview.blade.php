<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview WhatsApp - {{ ucfirst($type) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-md mx-auto">
        {{-- Phone Frame --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border-8 border-gray-800">
            {{-- Phone Notch --}}
            <div class="bg-gray-800 h-8 relative">
                <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-40 h-6 bg-black rounded-b-2xl"></div>
            </div>

            {{-- WhatsApp Header --}}
            <div class="bg-green-600 text-white p-4 flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-green-600 font-bold">
                    P
                </div>
                <div class="flex-1">
                    <p class="font-semibold">Panitia PSB PEMBDA</p>
                    <p class="text-xs opacity-90">Online</p>
                </div>
                <div class="flex gap-4">
                    <i class="fas fa-video-camera"></i>
                    <i class="fas fa-phone"></i>
                    <i class="fas fa-ellipsis-v"></i>
                </div>
            </div>

            {{-- Chat Area --}}
            <div class="bg-gray-50 p-4 min-h-96" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;100&quot; height=&quot;100&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Crect fill=&quot;%23f3f4f6&quot; width=&quot;100&quot; height=&quot;100&quot;/%3E%3Cg%3E%3Crect fill=&quot;%23e5e7eb&quot; x=&quot;25&quot; y=&quot;25&quot; width=&quot;2&quot; height=&quot;2&quot;/%3E%3C/g%3E%3C/svg%3E')">
                {{-- Incoming Message --}}
                <div class="flex justify-end mb-4">
                    <div class="bg-green-100 rounded-2xl rounded-tr-sm px-4 py-3 max-w-xs shadow-sm">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $message }}</p>
                        <div class="flex items-center justify-end gap-1 mt-2">
                            <span class="text-xs text-gray-500">{{ now()->format('H:i') }}</span>
                            <i class="fas fa-check-double text-blue-500 text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- Timestamp --}}
                <div class="text-center mb-4">
                    <span class="bg-white px-3 py-1 rounded-full text-xs text-gray-600 shadow-sm">
                        {{ now()->format('d/m/Y') }}
                    </span>
                </div>

                {{-- Info --}}
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-lg text-sm">
                    <p class="font-bold text-yellow-800 mb-1">📱 Preview WhatsApp</p>
                    <p class="text-yellow-700 text-xs">Ini adalah preview. Pesan tidak benar-benar dikirim.</p>
                    <div class="mt-2 pt-2 border-t border-yellow-200">
                        <p class="text-xs text-yellow-700"><strong>Tipe:</strong> {{ ucfirst(str_replace('_', ' ', $type)) }}</p>
                        <p class="text-xs text-yellow-700"><strong>Kepada:</strong> {{ $applicant->full_name }}</p>
                        <p class="text-xs text-yellow-700"><strong>No. HP:</strong> {{ $applicant->phone }}</p>
                    </div>
                </div>
            </div>

            {{-- Input Area --}}
            <div class="bg-white border-t border-gray-200 p-3 flex items-center gap-3">
                <i class="fas fa-smile text-gray-400 text-xl"></i>
                <div class="flex-1 bg-gray-100 rounded-full px-4 py-2">
                    <input type="text" placeholder="Type a message" class="bg-transparent w-full outline-none text-sm" disabled>
                </div>
                <i class="fas fa-microphone text-gray-400 text-xl"></i>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex gap-3">
            <button onclick="window.close()" class="flex-1 px-4 py-3 bg-gray-600 text-white rounded-xl font-semibold hover:bg-gray-700">
                <i class="fas fa-times mr-2"></i>Tutup
            </button>
            <button onclick="copyMessage()" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700">
                <i class="fas fa-copy mr-2"></i>Copy Text
            </button>
        </div>
    </div>

    <script>
        function copyMessage() {
            const message = `{{ str_replace(["\n", "\r"], " ", $message) }}`;
            navigator.clipboard.writeText(message).then(() => {
                alert('Pesan berhasil dicopy!');
            });
        }
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>

