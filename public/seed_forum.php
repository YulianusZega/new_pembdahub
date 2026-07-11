<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

if (!isset($_GET['token']) || $_GET['token'] !== 'pembda2026seed') {
    die("Akses ditolak. Token tidak valid.");
}

// Fetch random students from database
$students = Student::with(['user', 'school'])
    ->whereHas('user')
    ->inRandomOrder()->limit(30)->get();

if ($students->isEmpty()) {
    die("Belum ada data siswa di database untuk melakukan simulasi.");
}

$users = [];
foreach ($students as $s) {
    if ($s->user_id) {
        $users[] = $s->user_id;
    }
}

if (empty($users)) {
    die("Tidak ada user_id yang ditemukan.");
}

function randomUser($users) {
    return $users[array_rand($users)];
}

function randomUserExcluding($users, $excludeId) {
    $u = randomUser($users);
    $attempts = 0;
    while ($u == $excludeId && $attempts < 10) {
        $u = randomUser($users);
        $attempts++;
    }
    return $u;
}

// Copy images to storage
if (!Storage::disk('public')->exists('forum')) {
    Storage::disk('public')->makeDirectory('forum');
}

$dummyImagesDir = __DIR__ . '/forum_dummy_images';
$images = [
    'math' => 'math_homework_1783774463991.png',
    'esports' => 'esports_poster_1783774481328.png',
    'anime' => 'anime_sketch_1783774495898.png'
];

foreach ($images as $key => $file) {
    $sourcePath = $dummyImagesDir . '/' . $file;
    if (file_exists($sourcePath)) {
        copy($sourcePath, storage_path('app/public/forum/' . $file));
    }
}

function addReactions($type, $id, $users) {
    $emojis = ['🔥','❤️','😂','👍','👀','👏','💡'];
    $count = rand(2, 8);
    for ($i=0; $i<$count; $i++) {
        try {
            DB::table('forum_reactions')->insert([
                'forum_thread_id' => $type === 'thread' ? $id : null,
                'forum_reply_id' => $type === 'reply' ? $id : null,
                'user_id' => randomUser($users),
                'emoji' => $emojis[array_rand($emojis)],
                'created_at' => Carbon::now()->subMinutes(rand(1, 100)),
                'updated_at' => Carbon::now()
            ]);
        } catch (\Exception $e) {}
    }
}

// Delete existing simulated threads to prevent duplicates if run multiple times?
// Let's just create them. If it runs multiple times, it creates more traffic.

// Thread 1: Gaming
$u1 = randomUser($users);
$t1 = ForumThread::create([
    'user_id' => $u1,
    'title' => 'Mabar MLBB Malam Ini! Butuh Jungler & Roamer (Semua Unit Bebas) 🔥',
    'content' => 'Halo anak Pembda! Malam ini jam 8 kita mau push rank bareng. Udah ada 3 slot, kurang Jungler sama Roamer nih. Yang mythic honor ke atas langsung komen ya! Jangan lupa bawa mic wkwkw.',
    'category' => 'gaming',
    'image_path' => 'forum/' . $images['esports'],
    'created_at' => Carbon::now()->subHours(5)
]);
$poll1Id = DB::table('forum_polls')->insertGetId([
    'forum_thread_id' => $t1->id, 'question' => 'Kamu biasanya main role apa?', 'is_multiple_choice' => false, 'created_at' => Carbon::now()
]);
DB::table('forum_poll_options')->insert([
    ['forum_poll_id' => $poll1Id, 'option_text' => 'Jungler (Retri Indomaret)'],
    ['forum_poll_id' => $poll1Id, 'option_text' => 'Roamer (Tumbal)'],
    ['forum_poll_id' => $poll1Id, 'option_text' => 'Gold Lane (Tolong Gank)'],
    ['forum_poll_id' => $poll1Id, 'option_text' => 'Mage (Tukang Sampah)']
]);
$r1_1 = ForumReply::create(['forum_thread_id' => $t1->id, 'user_id' => randomUserExcluding($users, $u1), 'content' => 'Bang, aku roamer nih, Mythic 25 bintang, boleh join gak?', 'created_at' => Carbon::now()->subHours(4)]);
$r1_2 = ForumReply::create(['forum_thread_id' => $t1->id, 'user_id' => randomUserExcluding($users, $u1), 'parent_reply_id' => $r1_1->id, 'content' => 'Boleh tuh, tapi lu mainnya sabar gak? kemaren gue ketemu roamer mukil 😭', 'created_at' => Carbon::now()->subHours(3)]);
$r1_3 = ForumReply::create(['forum_thread_id' => $t1->id, 'user_id' => $u1, 'parent_reply_id' => $r1_2->id, 'content' => 'Wkwkwk santai, nanti kita atur tempo. Pokoknya jam 8 malam online ya!', 'created_at' => Carbon::now()->subHours(2)]);
addReactions('thread', $t1->id, $users);
addReactions('reply', $r1_1->id, $users);

// Thread 2: Tanya Jawab (Math)
$u2 = randomUser($users);
$t2 = ForumThread::create([
    'user_id' => $u2,
    'title' => 'TOLONG BANGET 😭 Ada yang paham Limit Trigonometri ini nggak?',
    'content' => 'Guys besok aku ada ulangan MTK sama Pak Budi. Dari tadi stuck ngerjain soal limit yang ini. Udah dicoba pake L\'Hopital malah makin muter-muter. Ada yang bisa bantu jabarin?',
    'category' => 'tanya_jawab',
    'image_path' => 'forum/' . $images['math'],
    'created_at' => Carbon::now()->subHours(24)
]);
$r2_1 = ForumReply::create(['forum_thread_id' => $t2->id, 'user_id' => randomUserExcluding($users, $u2), 'content' => 'Itu pake rumus identitas sudut rangkap dulu dek. $\cos(2x) = 1 - 2\sin^2(x)$. Coba diubah dulu bentuknya.', 'created_at' => Carbon::now()->subHours(23)]);
$r2_2 = ForumReply::create(['forum_thread_id' => $t2->id, 'user_id' => $u2, 'parent_reply_id' => $r2_1->id, 'content' => 'Oalahh pantesan! Makasih kak pencerahannya, langsung ketemu jawabannya wkwk. Btw kakak jago banget, sering ikut olimpiade ya?', 'created_at' => Carbon::now()->subHours(22)]);
addReactions('thread', $t2->id, $users);
addReactions('reply', $r2_1->id, $users);

// Thread 3: Karya Seni (Anime)
$u3 = randomUser($users);
$t3 = ForumThread::create([
    'user_id' => $u3,
    'title' => 'Iseng-iseng nge-gambar pas jam kosong wkwk 🎨✨',
    'content' => 'Lagi bosen nunggu guru rapat, akhirnya nyoret-nyoret di iPad pinjeman temen. Belum kelar sih shading-nya. Rate dong 1-10! Kritik dan saran sangat diterima.',
    'category' => 'art_gallery',
    'image_path' => 'forum/' . $images['anime'],
    'created_at' => Carbon::now()->subHours(48)
]);
$r3_1 = ForumReply::create(['forum_thread_id' => $t3->id, 'user_id' => randomUserExcluding($users, $u3), 'content' => 'Wah sumpah keren banget!! 100/10 😍 Pake aplikasi apa itu kak?', 'created_at' => Carbon::now()->subHours(47)]);
$r3_2 = ForumReply::create(['forum_thread_id' => $t3->id, 'user_id' => $u3, 'parent_reply_id' => $r3_1->id, 'content' => 'Pake Procreate dek hehe. Makasih yaa!', 'created_at' => Carbon::now()->subHours(46)]);
$r3_3 = ForumReply::create(['forum_thread_id' => $t3->id, 'user_id' => randomUserExcluding($users, $u3), 'content' => 'Warnanya nyala banget kak! Detail rambutnya juga gila sih. Kapan-kapan bikin tutorial mewarnai dong di channel ini.', 'created_at' => Carbon::now()->subHours(40)]);
addReactions('thread', $t3->id, $users);

// Thread 4: Diskusi Lobi
$u4 = randomUser($users);
$t4 = ForumThread::create([
    'user_id' => $u4,
    'title' => 'Guys jujurly, kantin sekolah kita enaknya ditambah menu apa ya? 🤔',
    'content' => 'Tiap hari makan gorengan sama mie rebus lama-lama bosen juga wkwk. Kalo kalian pengennya ada jajanan apa di kantin Pembda?',
    'category' => 'diskusi',
    'created_at' => Carbon::now()->subMinutes(30)
]);
ForumReply::create(['forum_thread_id' => $t4->id, 'user_id' => randomUserExcluding($users, $u4), 'content' => 'SEBLAK PEDES LEVEL DEWA!! 🔥', 'created_at' => Carbon::now()->subMinutes(25)]);
ForumReply::create(['forum_thread_id' => $t4->id, 'user_id' => randomUserExcluding($users, $u4), 'content' => 'Kopi kekinian kek Janji Jiwa gitu, biar gak ngantuk pas pelajaran Sejarah wkwk', 'created_at' => Carbon::now()->subMinutes(20)]);
ForumReply::create(['forum_thread_id' => $t4->id, 'user_id' => randomUserExcluding($users, $u4), 'content' => 'Ayam geprek mozarella pls, yang jual ibu kantin ujung enak banget sebenernya kalo dibikin menu baru.', 'created_at' => Carbon::now()->subMinutes(15)]);
addReactions('thread', $t4->id, $users);

// Thread 5: Music
$u5 = randomUser($users);
$t5 = ForumThread::create([
    'user_id' => $u5,
    'title' => 'Cari Bassist & Drummer buat Pensi Bulan Depan! 🎸🥁',
    'content' => 'Gue (Vokal) sama temen gue (Gitar) lagi cari personel tambahan buat manggung di acara pensi yayasan. Alirannya Pop Punk / Alt Rock (Paramore, Naff, dll). Latihan tiap sabtu sore di studio deket sekolah. Ada yang minat?',
    'category' => 'performance',
    'created_at' => Carbon::now()->subDays(2)
]);
$poll2Id = DB::table('forum_polls')->insertGetId([
    'forum_thread_id' => $t5->id, 'question' => 'Kalian biasanya main instrumen apa?', 'is_multiple_choice' => true, 'created_at' => Carbon::now()
]);
DB::table('forum_poll_options')->insert([
    ['forum_poll_id' => $poll2Id, 'option_text' => 'Gitar Akustik'], ['forum_poll_id' => $poll2Id, 'option_text' => 'Gitar Elektrik'], ['forum_poll_id' => $poll2Id, 'option_text' => 'Bass'], ['forum_poll_id' => $poll2Id, 'option_text' => 'Drum / Cajon'], ['forum_poll_id' => $poll2Id, 'option_text' => 'Vokal aja doang']
]);

echo "<h2>Sukses!</h2>";
echo "<p>Data simulasi forum heboh berhasil dimasukkan ke dalam database Hostinger!</p>";
echo "<p>Silakan buka <a href='/forum'>Halaman Forum Pembda Space</a> untuk melihat keramaiannya.</p>";
