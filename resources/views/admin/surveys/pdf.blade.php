<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Survei - {{ $survey->title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 11px; color: #1a202c; line-height: 1.5; background: #fff; }
        .header-wrap { margin-bottom: 18px; border-bottom: 3px solid #4f46e5; padding-bottom: 12px; }
        .header-top { text-align: center; margin-bottom: 8px; }
        .header-top h1 { font-size: 17px; color: #1e1b4b; text-transform: uppercase; letter-spacing: 1px; font-weight: 900; }
        .header-top .subtitle { font-size: 11px; color: #6366f1; font-weight: bold; margin-top: 2px; }
        .header-top .instansi { font-size: 12px; color: #374151; margin-top: 2px; }
        .meta-box { background: #f0f4ff; border: 1px solid #c7d2fe; border-radius: 4px; padding: 10px 12px; margin-bottom: 16px; }
        .meta-box table { width: 100%; border: none; border-collapse: collapse; }
        .meta-box td { padding: 3px 6px; border: none; font-size: 11px; vertical-align: top; }
        .meta-box td.label { font-weight: bold; color: #3730a3; width: 115px; white-space: nowrap; }
        .meta-box td.colon { width: 10px; }
        .section-title { background: #1e1b4b; color: #fff; padding: 6px 12px; font-size: 12px; font-weight: bold; margin-top: 20px; margin-bottom: 0; border-radius: 3px 3px 0 0; }
        .section-subtitle { font-size: 10px; color: #a5b4fc; font-weight: normal; margin-left: 4px; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #c7d2fe; border-top: none; }
        .summary-table th { background: #4f46e5; color: #fff; padding: 7px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; border: 1px solid #4338ca; }
        .summary-table th.left { text-align: left; }
        .summary-table td { padding: 6px 8px; border: 1px solid #dde3ff; vertical-align: middle; font-size: 11px; }
        .summary-table tr:nth-child(even) td { background: #f5f7ff; }
        .summary-table tr:nth-child(odd) td { background: #ffffff; }
        .q-no { text-align: center; font-weight: 900; color: #4f46e5; width: 30px; }
        .q-text { font-weight: 600; color: #1e293b; }
        .avg-cell { text-align: center; }
        .score-excellent { color: #065f46; background: #d1fae5; border: 1px solid #6ee7b7; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 10px; }
        .score-good { color: #1e40af; background: #dbeafe; border: 1px solid #93c5fd; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 10px; }
        .score-moderate { color: #92400e; background: #fef3c7; border: 1px solid #fbbf24; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 10px; }
        .score-poor { color: #991b1b; background: #fee2e2; border: 1px solid #fca5a5; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 10px; }
        .question-block { margin-bottom: 14px; border: 1px solid #e0e7ff; border-radius: 4px; page-break-inside: avoid; }
        .question-block-header { background: #eef2ff; padding: 7px 10px; border-bottom: 1px solid #c7d2fe; }
        .qnum { display: inline-block; background: #4f46e5; color: #fff; font-weight: 900; font-size: 10px; border-radius: 3px; padding: 1px 6px; margin-right: 6px; }
        .qtext { font-weight: 700; color: #1e1b4b; font-size: 11px; }
        .qtype { font-size: 9px; color: #6366f1; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 3px; }
        .question-block-body { padding: 8px 10px; }
        .dist-table { width: 100%; border-collapse: collapse; }
        .dist-table th { background: #4f46e5; color: #fff; padding: 5px 8px; font-size: 10px; text-align: center; border: 1px solid #4338ca; }
        .dist-table td { padding: 5px 8px; border: 1px solid #dde3ff; text-align: center; font-size: 11px; font-weight: 600; vertical-align: middle; }
        .dist-table td.rating-label { text-align: left; font-weight: 700; }
        .bar-fill { display: inline-block; height: 11px; background: #6366f1; vertical-align: middle; border-radius: 2px; min-width: 2px; }
        .bar-pct { display: inline-block; font-size: 10px; font-weight: 900; color: #1e1b4b; margin-left: 4px; vertical-align: middle; }
        .dist-summary { margin-top: 8px; background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 3px; padding: 6px 10px; }
        .dist-summary table { width: 100%; border: none; border-collapse: collapse; }
        .dist-summary td { border: none; padding: 2px 6px; font-size: 11px; vertical-align: middle; }
        .ds-label { font-weight: bold; color: #5b21b6; white-space: nowrap; }
        .ds-val { font-weight: 900; color: #1e1b4b; font-size: 13px; }
        .rank-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #c7d2fe; border-top: none; }
        .rank-table th { background: #0f766e; color: #fff; padding: 7px 8px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: center; border: 1px solid #0d9488; }
        .rank-table th.left { text-align: left; }
        .rank-table td { padding: 6px 8px; border: 1px solid #99f6e4; font-size: 11px; vertical-align: middle; }
        .rank-table tr:nth-child(even) td { background: #f0fdf4; }
        .rank-table tr:nth-child(odd) td { background: #ffffff; }
        .essay-question-header { background: #374151; color: #fff; padding: 6px 10px; font-weight: bold; font-size: 11px; margin-bottom: 0; }
        .essay-list { border: 1px solid #d1d5db; margin-bottom: 14px; }
        .essay-item { padding: 8px 10px; border-bottom: 1px dashed #e5e7eb; page-break-inside: avoid; }
        .essay-item:last-child { border-bottom: none; }
        .essay-author { font-weight: bold; color: #1d4ed8; font-size: 10px; margin-bottom: 3px; }
        .essay-badge { display: inline-block; background: #374151; color: #fff; padding: 1px 5px; border-radius: 2px; font-size: 9px; margin-left: 4px; }
        .essay-badge-green { background: #15803d; }
        .essay-text { color: #374151; font-style: italic; font-size: 11px; }
        .no-answer { color: #9ca3af; font-style: italic; font-size: 11px; padding: 8px 10px; }
        .footer { margin-top: 25px; border-top: 1px solid #c7d2fe; padding-top: 8px; text-align: center; font-size: 9px; color: #9ca3af; }
        .page-break { page-break-after: always; }
        .text-center { text-align: center; }
        .no-wrap { white-space: nowrap; }
        .detail-wrap { margin-top: 0; border: 1px solid #c7d2fe; border-top: none; padding: 12px; margin-bottom: 20px; background: #fff; }
    </style>
</head>
<body>

    <div class="header-wrap">
        <div class="header-top">
            <h1>Laporan Hasil Survei Kepuasan</h1>
            <div class="instansi">Perguruan PEMBDA &mdash; SMKS Swasta Pembda Nias</div>
            <div class="subtitle">Dokumen Evaluasi Resmi &mdash; Bersifat Rahasia</div>
        </div>
    </div>

    <div class="meta-box">
        <table>
            <tr>
                <td class="label">Judul Survei</td>
                <td class="colon">:</td>
                <td>{{ $survey->title }}</td>
                <td class="label">Target Responden</td>
                <td class="colon">:</td>
                <td><strong>{{ strtoupper($survey->target_respondent) }}</strong></td>
            </tr>
            <tr>
                <td class="label">Total Partisipasi</td>
                <td class="colon">:</td>
                <td><strong>{{ $totalResponses }}</strong> responden
                    @if(isset($totalTargetUsers) && $totalTargetUsers > 0)
                        dari {{ $totalTargetUsers }} ({{ round(($totalResponses/$totalTargetUsers)*100,1) }}%)
                    @endif
                </td>
                <td class="label">Filter Guru</td>
                <td class="colon">:</td>
                <td>{{ $teacherType ? strtoupper($teacherType) : 'Semua Tipe Guru' }}</td>
            </tr>
            <tr>
                <td class="label">Status Survei</td>
                <td class="colon">:</td>
                <td>{{ strtoupper($survey->status) }}</td>
                <td class="label">Tanggal Cetak</td>
                <td class="colon">:</td>
                <td>{{ now()->format('d M Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    @php $scaleResults = collect($results)->where('type', 'scale')->values(); @endphp

    @if($scaleResults->count() > 0)
    <div class="section-title">
        A. Rekapitulasi Pertanyaan (Skala)
        <span class="section-subtitle">&mdash; Ringkasan Skor Rata-rata per Indikator</span>
    </div>
    <table class="summary-table">
        <thead>
            <tr>
                <th class="left" style="width:4%">No</th>
                <th class="left" style="width:56%">Pertanyaan / Indikator</th>
                <th style="width:12%">Tipe Skala</th>
                <th style="width:12%">Responden</th>
                <th style="width:16%">Rata-rata Skor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scaleResults as $i => $res)
            @php
                $avg = floatval($res['average']);
                $maxScale = $res['scale_type'] === 'likert_4' ? 4 : 5;
                $pct = $res['scale_type'] === 'yes_no' ? $avg : ($avg / $maxScale * 100);
                if ($res['scale_type'] === 'yes_no') {
                    $badgeClass = $avg >= 85 ? 'score-excellent' : ($avg >= 65 ? 'score-good' : ($avg >= 40 ? 'score-moderate' : 'score-poor'));
                    $avgDisplay = $avg . '% Menjawab "Ya"';
                } else {
                    $badgeClass = $pct >= 85 ? 'score-excellent' : ($pct >= 70 ? 'score-good' : ($pct >= 50 ? 'score-moderate' : 'score-poor'));
                    $avgDisplay = $avg . ' / ' . $maxScale . '.0';
                }
            @endphp
            <tr>
                <td class="q-no">{{ $i + 1 }}</td>
                <td class="q-text">{{ $res['question']->question_text }}</td>
                <td class="text-center no-wrap">{{ str_replace('_', ' ', strtoupper($res['scale_type'])) }}</td>
                <td class="text-center">{{ $res['total_answers'] }} org</td>
                <td class="avg-cell"><span class="{{ $badgeClass }}">{{ $avgDisplay }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">
        A2. Distribusi Jawaban per Pertanyaan (Detail)
        <span class="section-subtitle">&mdash; Persebaran pilihan jawaban setiap responden</span>
    </div>
    <div class="detail-wrap">
    @foreach($scaleResults as $i => $res)
    @php
        $avg = floatval($res['average']);
        $maxS = ($res['scale_type'] === 'yes_no') ? 1 : (($res['scale_type'] === 'likert_4') ? 4 : 5);
        $minS = ($res['scale_type'] === 'yes_no') ? 0 : 1;
        $totalAns = $res['total_answers'];
        $pctAvg = ($res['scale_type'] === 'yes_no') ? $avg : ($maxS > 0 ? round(($avg/$maxS)*100,1) : 0);
        if ($res['scale_type'] === 'yes_no') {
            $badgeC = $avg >= 85 ? 'score-excellent' : ($avg >= 65 ? 'score-good' : ($avg >= 40 ? 'score-moderate' : 'score-poor'));
        } else {
            $badgeC = $pctAvg >= 85 ? 'score-excellent' : ($pctAvg >= 70 ? 'score-good' : ($pctAvg >= 50 ? 'score-moderate' : 'score-poor'));
        }
        $barColors = [5=>'#10b981',4=>'#3b82f6',3=>'#f59e0b',2=>'#f97316',1=>'#ef4444',0=>'#ef4444'];
    @endphp
    <div class="question-block">
        <div class="question-block-header">
            <span class="qnum">P{{ $i + 1 }}</span>
            <span class="qtext">{{ $res['question']->question_text }}</span>
            <div class="qtype">
                Tipe: {{ str_replace('_', ' ', strtoupper($res['scale_type'])) }}
                &nbsp;|&nbsp; Total Dijawab: {{ $totalAns }} responden
            </div>
        </div>
        <div class="question-block-body">
            <table class="dist-table">
                <thead>
                    <tr>
                        <th style="width:5%; text-align:center">Skor</th>
                        <th style="width:28%; text-align:left">Keterangan Jawaban</th>
                        <th style="width:9%">Jumlah</th>
                        <th style="width:38%">Distribusi Visual</th>
                        <th style="width:10%">Persentase</th>
                        <th style="width:10%">Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @for($rating = $maxS; $rating >= $minS; $rating--)
                    @php
                        $dist = $res['distribution'][$rating] ?? ['count'=>0,'percentage'=>0];
                        $cnt = $dist['count'];
                        $distPct = $dist['percentage'];
                        $barW = min(intval($distPct), 100);
                        $barC = $barColors[$rating] ?? '#6366f1';
                        if ($res['scale_type'] === 'yes_no') {
                            $label = ($rating === 1) ? 'Ya / Sesuai / Setuju' : 'Tidak / Kurang / Tidak Sesuai';
                        } elseif ($res['scale_type'] === 'competence_5') {
                            $ll = [5=>'Sangat Menguasai',4=>'Menguasai / Baik',3=>'Cukup Menguasai',2=>'Kurang Menguasai',1=>'Sangat Kurang'];
                            $label = $ll[$rating] ?? '-';
                        } elseif ($res['scale_type'] === 'likert_4') {
                            $ll = [4=>'Sangat Setuju',3=>'Setuju',2=>'Tidak Setuju',1=>'Sangat Tidak Setuju'];
                            $label = $ll[$rating] ?? '-';
                        } else {
                            $ll = [5=>'Sangat Setuju',4=>'Setuju',3=>'Ragu-ragu / Netral',2=>'Tidak Setuju',1=>'Sangat Tidak Setuju'];
                            $label = $ll[$rating] ?? '-';
                        }
                    @endphp
                    <tr>
                        <td style="text-align:center; font-weight:900; color:{{ $barC }}; font-size:13px;">{{ $rating }}</td>
                        <td class="rating-label">{{ $label }}</td>
                        <td style="text-align:center; font-weight:700;">{{ $cnt }}</td>
                        <td>
                            <span class="bar-fill" style="width:{{ $barW }}%; background:{{ $barC }};"></span>
                            <span class="bar-pct">{{ $distPct }}%</span>
                        </td>
                        <td style="text-align:center; font-weight:700;">{{ $distPct }}%</td>
                        <td style="text-align:center; font-weight:700; color:#6366f1;">{{ $cnt * $rating }}</td>
                    </tr>
                    @endfor
                </tbody>
            </table>
            <div class="dist-summary">
                <table>
                    <tr>
                        <td class="ds-label">Total Jawaban:</td>
                        <td><span class="ds-val">{{ $totalAns }}</span> responden</td>
                        <td class="ds-label">Rata-rata Skor:</td>
                        <td>
                            @if($res['scale_type'] === 'yes_no')
                                <span class="{{ $badgeC }}">{{ $avg }}% Menjawab "Ya"</span>
                            @else
                                <span class="{{ $badgeC }}">{{ $avg }} / {{ $maxS }}.0</span>
                            @endif
                        </td>
                        <td class="ds-label">Indeks Kepuasan:</td>
                        <td><strong>{{ $pctAvg }}%</strong></td>
                    </tr>
                </table>
            </div>

            {{-- Per-Question AI Insight --}}
            @php
                if ($pctAvg >= 85) {
                    $qInsightTitle = 'Sangat Memuaskan (Excellence)';
                    $qInsightDesc = 'Indikator ini menunjukkan tingkat kepuasan superior. Responden sangat terkesan dan menilai aspek ini sangat positif. Layak dijadikan benchmark dan dipertahankan sebagai standar acuan institusi.';
                    $qInsightColor = '#065f46'; $qInsightBg = '#d1fae5'; $qInsightBorder = '#6ee7b7';
                } elseif ($pctAvg >= 70) {
                    $qInsightTitle = 'Memuaskan (Good)';
                    $qInsightDesc = 'Secara keseluruhan aspek ini berjalan lancar dan optimal. Responden menilai positif namun masih ada ruang untuk inovasi dan peningkatan efisiensi pada detail implementasi.';
                    $qInsightColor = '#1e40af'; $qInsightBg = '#dbeafe'; $qInsightBorder = '#93c5fd';
                } elseif ($pctAvg >= 50) {
                    $qInsightTitle = 'Cukup / Perlu Perhatian (Moderate)';
                    $qInsightDesc = 'Hasil menunjukkan tingkat kepuasan yang biasa saja. Tidak ada keluhan fatal namun kurang ada impresi kuat dari responden. Perlu penyegaran strategi dan evaluasi lebih mendalam pada aspek ini.';
                    $qInsightColor = '#92400e'; $qInsightBg = '#fef3c7'; $qInsightBorder = '#fbbf24';
                } else {
                    $qInsightTitle = 'Di Bawah Standar — Perlu Tindak Lanjut (Poor)';
                    $qInsightDesc = 'Indikator ini mendeteksi adanya kendala atau keluhan serius dari responden. Tingkat kepuasan rendah mengisyaratkan perlunya perombakan sistem atau kebijakan yang mendasari aspek ini segera.';
                    $qInsightColor = '#991b1b'; $qInsightBg = '#fee2e2'; $qInsightBorder = '#fca5a5';
                }
            @endphp
            <div style="margin-top:8px; background:{{ $qInsightBg }}; border:1px solid {{ $qInsightBorder }}; border-radius:4px; padding:8px 12px;">
                <table style="width:100%; border:none; border-collapse:collapse;">
                    <tr>
                        <td style="border:none; padding:2px 6px; width:16px; font-size:14px; color:{{ $qInsightColor }}; vertical-align:middle;">&#128202;</td>
                        <td style="border:none; padding:2px 6px; vertical-align:middle;">
                            <strong style="font-size:11px; color:{{ $qInsightColor }};">Kesimpulan Analitik: {{ $qInsightTitle }}</strong><br>
                            <span style="font-size:10px; color:#374151;">{{ $qInsightDesc }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endforeach
    </div>
    @else
    <div class="section-title">A. Rekapitulasi Pertanyaan (Skala)</div>
    <p style="padding:12px; border:1px solid #e5e7eb; border-top:none; color:#9ca3af; font-style:italic;">Tidak ada pertanyaan berskala dalam survei ini.</p>
    @endif

    {{-- ====== SECTION D: AI OVERALL CONCLUSION ====== --}}
    @if($scaleResults->count() > 0)
    @php
        // Compute overall average across all scale questions
        $allAvgs = $scaleResults->filter(fn($r) => $r['scale_type'] !== 'yes_no' && $r['total_answers'] > 0)->pluck('average');
        $overallAvg = $allAvgs->count() > 0 ? round($allAvgs->avg(), 2) : null;
        $overallPct = $overallAvg ? round(($overallAvg / 5) * 100, 1) : null;

        $yesNoAvgs = $scaleResults->filter(fn($r) => $r['scale_type'] === 'yes_no' && $r['total_answers'] > 0)->pluck('average');
        $overallYesNoPct = $yesNoAvgs->count() > 0 ? round($yesNoAvgs->avg(), 1) : null;

        // Determine AI tier
        $checkPct = $overallPct ?? $overallYesNoPct ?? 0;
        if ($checkPct >= 85) {
            $aiTier = 'SANGAT MEMUASKAN (Excellence)';
            $aiColor = '#065f46'; $aiBg = '#d1fae5'; $aiBorder = '#6ee7b7';
            $aiNarrative = 'Berdasarkan agregasi data seluruh indikator survei, kondisi umum menunjukkan tingkat keselarasan dan kepuasan yang berada pada level superior. Seluruh aspek utama yang diukur mendapat respons sangat positif dari para responden. Institusi telah berhasil mengimplementasikan standar yang ditetapkan dengan sangat baik.';
            $aiRecomTitle = 'Rekomendasi Strategis AI';
            $aiRecom = 'Pertahankan standar yang telah tercapai sebagai benchmark nasional. Lakukan dokumentasi best practices dan jadikan survei ini sebagai acuan untuk replikasi di unit kerja lain. Pertimbangkan pemberian apresiasi institusional kepada pihak-pihak yang berkontribusi dalam pencapaian ini.';
            $kebijakanTitle = 'Keputusan Kebijakan Yayasan: Apresiasi & Replikasi';
            $kebijakan = 'Yayasan sangat direkomendasikan untuk memberikan apresiasi formal (piagam/penghargaan) kepada satuan pendidikan ini. Hasil survei layak dipublikasikan sebagai contoh nyata keberhasilan implementasi program Yayasan. Pimpinan dapat menugaskan sekolah ini sebagai sekolah percontohan (pilot school) dalam program akselerasi mutu regional.';
            $kebijakanColor = '#065f46'; $kebijakanBg = '#d1fae5'; $kebijakanBorder = '#6ee7b7';
        } elseif ($checkPct >= 70) {
            $aiTier = 'MEMUASKAN (Good)';
            $aiColor = '#1e40af'; $aiBg = '#dbeafe'; $aiBorder = '#93c5fd';
            $aiNarrative = 'Data agregat survei mengindikasikan capaian yang baik dan sesuai dengan ekspektasi institusi secara umum. Sebagian besar indikator kritis menunjukkan performa positif, meskipun analisa mendeteksi beberapa area spesifik yang masih memiliki potensi signifikan untuk ditingkatkan lebih lanjut menuju level Excellence.';
            $aiRecomTitle = 'Rekomendasi Strategis AI';
            $aiRecom = 'Lakukan identifikasi mendalam pada 3-5 indikator dengan skor terendah dan jadikan sebagai fokus perbaikan prioritas semester berikutnya. Pertimbangkan program pelatihan bertarget untuk memperkuat area yang masih moderat. Survei lanjutan dalam 6 bulan ke depan sangat direkomendasikan untuk memantau progres.';
            $kebijakanTitle = 'Keputusan Kebijakan Yayasan: Penguatan Terarah';
            $kebijakan = 'Yayasan merekomendasikan alokasi program pembinaan spesifik berdasarkan indikator gap yang teridentifikasi. Kepala Sekolah diharapkan menyusun Rencana Aksi (Action Plan) perbaikan dan melaporkannya kepada Yayasan dalam 30 hari kalender. Supervisi rutin dari pengawas Yayasan perlu dijadwalkan.';
            $kebijakanColor = '#1e40af'; $kebijakanBg = '#dbeafe'; $kebijakanBorder = '#93c5fd';
        } elseif ($checkPct >= 50) {
            $aiTier = 'CUKUP — PERLU PERHATIAN (Moderate)';
            $aiColor = '#92400e'; $aiBg = '#fef3c7'; $aiBorder = '#fbbf24';
            $aiNarrative = 'Hasil agregasi survei menunjukkan capaian yang berada pada level menengah. Terdapat beberapa indikator dengan skor cukup baik namun sejumlah area kritis menunjukkan kesenjangan yang perlu segera ditangani. Kondisi ini mengindikasikan adanya hambatan sistemik yang memerlukan intervensi manajemen yang lebih proaktif dan terstruktur.';
            $aiRecomTitle = 'Rekomendasi Strategis AI';
            $aiRecom = 'Segera bentuk tim task force internal untuk menginvestigasi akar masalah pada indikator berkategori "Di Bawah Standar". Susun program penyegaran menyeluruh dan libatkan seluruh stakeholder dalam proses evaluasi. Jadwalkan survei monitoring dalam 3 bulan ke depan sebagai kontrol.';
            $kebijakanTitle = 'Keputusan Kebijakan Yayasan: Intervensi Manajemen';
            $kebijakan = 'Yayasan memerintahkan Kepala Sekolah untuk segera menyusun dan menyerahkan laporan evaluasi mendalam beserta Rencana Perbaikan Menyeluruh (RPM) dalam 14 hari kalender. Yayasan akan mengirimkan tim supervisi khusus untuk melakukan asesmen lapangan dan mendampingi proses perbaikan secara langsung.';
            $kebijakanColor = '#92400e'; $kebijakanBg = '#fef3c7'; $kebijakanBorder = '#fbbf24';
        } else {
            $aiTier = 'DI BAWAH STANDAR — TINDAKAN SEGERA (Poor)';
            $aiColor = '#991b1b'; $aiBg = '#fee2e2'; $aiBorder = '#fca5a5';
            $aiNarrative = 'PERINGATAN KRITIS: Hasil analisa agregat mendeteksi bahwa capaian survei ini berada jauh di bawah standar kelayakan yang ditetapkan Yayasan. Mayoritas indikator menunjukkan tingkat kepuasan yang rendah, mengindikasikan adanya permasalahan mendasar dan sistemik yang sangat mendesak untuk ditangani segera oleh pimpinan Yayasan.';
            $aiRecomTitle = 'Rekomendasi Darurat AI';
            $aiRecom = 'Diperlukan tindakan darurat dan segera: (1) Audit menyeluruh oleh Yayasan dalam 7 hari, (2) Pemanggilan dan klarifikasi seluruh pihak terkait, (3) Moratorium kegiatan non-prioritas hingga perbaikan terukur tercapai, (4) Penetapan target KPI pemulihan yang jelas dan terukur dengan timeline ketat.';
            $kebijakanTitle = 'Tindakan Darurat Yayasan — PRIORITAS UTAMA';
            $kebijakan = 'Yayasan wajib mengaktifkan Protokol Perbaikan Darurat (PPD). Pimpinan satuan pendidikan dipanggil untuk sesi hearing formal dalam 3 hari kerja. Dipertimbangkan penugasan Pelaksana Tugas (Plt.) sementara jika perbaikan tidak menunjukkan kemajuan berarti dalam 30 hari. Seluruh program baru ditunda sampai kondisi stabil.';
            $kebijakanColor = '#991b1b'; $kebijakanBg = '#fee2e2'; $kebijakanBorder = '#fca5a5';
        }
    @endphp

    <div class="page-break"></div>

    <div class="section-title">
        D. Kesimpulan &amp; Rekomendasi Kebijakan (AI)
        <span class="section-subtitle">&mdash; Analisis agregat otomatis berdasarkan seluruh data survei</span>
    </div>
    <div style="border:1px solid {{ $aiBorder }}; border-top:none; background:{{ $aiBg }}; padding:14px; margin-bottom:0;">
        <table style="width:100%; border:none; border-collapse:collapse; margin-bottom:10px;">
            <tr>
                <td style="border:none; padding:4px 8px; width:50%; vertical-align:top;">
                    <div style="font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px; color:#6b7280; margin-bottom:4px;">Skor Rata-rata Keseluruhan</div>
                    <div style="font-size:22px; font-weight:900; color:{{ $aiColor }};">{{ $overallAvg ?? '-' }}<span style="font-size:12px; font-weight:600; color:#9ca3af;"> / 5.0</span></div>
                </td>
                <td style="border:none; padding:4px 8px; width:50%; vertical-align:top;">
                    <div style="font-size:9px; font-weight:bold; text-transform:uppercase; letter-spacing:0.5px; color:#6b7280; margin-bottom:4px;">Indeks Kepuasan Global</div>
                    <div style="font-size:22px; font-weight:900; color:{{ $aiColor }};">{{ $overallPct ?? '-' }}<span style="font-size:12px; font-weight:600; color:#9ca3af;">%</span></div>
                </td>
            </tr>
        </table>
        <div style="background:white; border:1px solid {{ $aiBorder }}; border-radius:4px; padding:10px 12px; margin-bottom:10px;">
            <div style="font-size:10px; font-weight:900; text-transform:uppercase; color:{{ $aiColor }}; margin-bottom:6px;">&#128201; Tingkat Capaian: {{ $aiTier }}</div>
            <p style="font-size:11px; color:#374151; line-height:1.6; margin:0 0 8px;">{{ $aiNarrative }}</p>
            <div style="background:{{ $aiBg }}; border-left:3px solid {{ $aiColor }}; padding:6px 10px; border-radius:0 3px 3px 0;">
                <div style="font-size:9px; font-weight:900; text-transform:uppercase; color:{{ $aiColor }}; margin-bottom:3px;">{{ $aiRecomTitle }}</div>
                <p style="font-size:10px; color:#374151; line-height:1.6; margin:0;">{{ $aiRecom }}</p>
            </div>
        </div>
    </div>

    {{-- ====== SECTION E: KEPUTUSAN KEBIJAKAN YAYASAN ====== --}}
    <div class="section-title" style="margin-top:0; border-radius:0;">
        E. Keputusan Kebijakan Yayasan
        <span class="section-subtitle">&mdash; Tindak lanjut resmi berdasarkan hasil analisis survei</span>
    </div>
    <div style="border:1px solid {{ $kebijakanBorder }}; border-top:none; background:{{ $kebijakanBg }}; padding:14px; margin-bottom:20px;">
        <div style="font-size:11px; font-weight:900; color:{{ $kebijakanColor }}; margin-bottom:8px;">&#127981; {{ $kebijakanTitle }}</div>
        <p style="font-size:11px; color:#1f2937; line-height:1.7; margin:0 0 16px;">{{ $kebijakan }}</p>
        <table style="width:100%; border-collapse:collapse; border:1px solid {{ $kebijakanBorder }};">
            <tr style="background:white;">
                <th style="border:1px solid {{ $kebijakanBorder }}; padding:6px 10px; font-size:10px; text-align:left; width:40%; color:{{ $kebijakanColor }};">Kolom Tanda Tangan</th>
                <th style="border:1px solid {{ $kebijakanBorder }}; padding:6px 10px; font-size:10px; text-align:center; width:30%;">Kepala Sekolah</th>
                <th style="border:1px solid {{ $kebijakanBorder }}; padding:6px 10px; font-size:10px; text-align:center; width:30%;">Pimpinan Yayasan</th>
            </tr>
            <tr>
                <td style="border:1px solid {{ $kebijakanBorder }}; padding:6px 10px; font-size:10px; color:#374151;">
                    Tanggal Penetapan Kebijakan:<br>
                    <strong>{{ now()->format('d M Y') }}</strong>
                </td>
                <td style="border:1px solid {{ $kebijakanBorder }}; padding:40px 10px 8px; text-align:center; font-size:10px; color:#9ca3af;">
                    (..............................)
                </td>
                <td style="border:1px solid {{ $kebijakanBorder }}; padding:40px 10px 8px; text-align:center; font-size:10px; color:#9ca3af;">
                    (..............................)
                </td>
            </tr>
        </table>
    </div>
    @endif

    <div class="page-break"></div>

    @if(isset($individualResponses) && $individualResponses->count() > 0)
    <div class="section-title">
        B. Peringkat Skor Individu Responden
        <span class="section-subtitle">&mdash; Diurutkan dari skor tertinggi ke terendah</span>
    </div>
    <table class="rank-table">
        <thead>
            <tr>
                <th style="width:7%">Peringkat</th>
                <th class="left" style="width:45%">Nama Responden</th>
                <th style="width:18%">Tipe</th>
                <th style="width:15%">Skor Akhir</th>
                <th style="width:15%">Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($individualResponses as $idx => $resp)
            @php
                $sc = is_numeric($resp->average_score) ? floatval($resp->average_score) : null;
                $pScr = $sc ? round(($sc/5)*100,1) : null;
                if (!$sc) { $cat = '-'; $catC = ''; }
                elseif ($pScr >= 85) { $cat = 'Sangat Baik'; $catC = 'score-excellent'; }
                elseif ($pScr >= 70) { $cat = 'Baik'; $catC = 'score-good'; }
                elseif ($pScr >= 50) { $cat = 'Cukup'; $catC = 'score-moderate'; }
                else { $cat = 'Kurang'; $catC = 'score-poor'; }
            @endphp
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $resp->user ? $resp->user->name : 'Anonim' }}</td>
                <td class="text-center">
                    @if($resp->teacher_type) Guru {{ ucfirst($resp->teacher_type) }}
                    @elseif($resp->user && $resp->user->isSiswa()) Siswa
                    @elseif($resp->user) {{ ucfirst($resp->user->role) }}
                    @else - @endif
                </td>
                <td class="text-center"><strong>{{ $resp->average_score }}</strong>{{ $sc ? ' / 5.0' : '' }}</td>
                <td class="text-center">
                    @if($catC)<span class="{{ $catC }}">{{ $cat }}</span>@else{{ $cat }}@endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ====== SECTION A3: GRAFIK VISUAL KESELURUHAN ====== --}}
    @if($scaleResults->count() > 0)
    <div class="page-break"></div>

    <div class="section-title">
        A3. Grafik Visual Rekapitulasi Keseluruhan
        <span class="section-subtitle">&mdash; Perbandingan skor rata-rata seluruh indikator dalam satu tampilan</span>
    </div>
    <div style="border:1px solid #c7d2fe; border-top:none; padding:16px 14px; margin-bottom:20px; background:#fff;">

        {{-- Legend --}}
        <table style="width:100%; border:none; border-collapse:collapse; margin-bottom:14px;">
            <tr>
                <td style="border:none; padding:2px 10px; font-size:9px; font-weight:900; text-transform:uppercase; color:#6b7280;">Legenda Kategori:</td>
                <td style="border:none; padding:2px 6px;">
                    <span style="display:inline-block; background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; padding:1px 6px; border-radius:3px; font-size:9px; font-weight:700;">&#9608; Excellence (85-100%)</span>
                </td>
                <td style="border:none; padding:2px 6px;">
                    <span style="display:inline-block; background:#dbeafe; color:#1e40af; border:1px solid #93c5fd; padding:1px 6px; border-radius:3px; font-size:9px; font-weight:700;">&#9608; Good (70-84%)</span>
                </td>
                <td style="border:none; padding:2px 6px;">
                    <span style="display:inline-block; background:#fef3c7; color:#92400e; border:1px solid #fbbf24; padding:1px 6px; border-radius:3px; font-size:9px; font-weight:700;">&#9608; Moderate (50-69%)</span>
                </td>
                <td style="border:none; padding:2px 6px;">
                    <span style="display:inline-block; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; padding:1px 6px; border-radius:3px; font-size:9px; font-weight:700;">&#9608; Poor (&lt;50%)</span>
                </td>
            </tr>
        </table>

        {{-- Bar Chart --}}
        <table style="width:100%; border:none; border-collapse:collapse;">
            @foreach($scaleResults as $ci => $res)
            @php
                $cAvg = floatval($res['average']);
                $cMax = $res['scale_type'] === 'likert_4' ? 4 : 5;
                $cPct = $res['scale_type'] === 'yes_no' ? $cAvg : round(($cAvg / $cMax) * 100, 1);
                // Bar width max 75% of cell to leave room for label
                $barW = min(intval($cPct * 0.74), 74);
                if ($cPct >= 85) {
                    $cBarColor = '#10b981'; $cBadgeBg = '#d1fae5'; $cBadgeColor = '#065f46'; $cBadgeBorder = '#6ee7b7';
                } elseif ($cPct >= 70) {
                    $cBarColor = '#3b82f6'; $cBadgeBg = '#dbeafe'; $cBadgeColor = '#1e40af'; $cBadgeBorder = '#93c5fd';
                } elseif ($cPct >= 50) {
                    $cBarColor = '#f59e0b'; $cBadgeBg = '#fef3c7'; $cBadgeColor = '#92400e'; $cBadgeBorder = '#fbbf24';
                } else {
                    $cBarColor = '#ef4444'; $cBadgeBg = '#fee2e2'; $cBadgeColor = '#991b1b'; $cBadgeBorder = '#fca5a5';
                }
                $rowBgC = $ci % 2 === 0 ? '#fafafa' : '#ffffff';
            @endphp
            <tr style="background:{{ $rowBgC }};">
                <td style="border:none; border-bottom:1px solid #f0f0f0; padding:5px 6px; width:4%; text-align:center; font-weight:900; color:#4f46e5; font-size:11px; vertical-align:middle;">P{{ $ci+1 }}</td>
                <td style="border:none; border-bottom:1px solid #f0f0f0; padding:5px 6px; width:32%; font-size:9px; font-weight:600; color:#374151; vertical-align:middle; line-height:1.3;">{{ mb_substr($res['question']->question_text, 0, 65) }}{{ mb_strlen($res['question']->question_text) > 65 ? '...' : '' }}</td>
                <td style="border:none; border-bottom:1px solid #f0f0f0; padding:5px 6px; width:48%; vertical-align:middle;">
                    <div style="background:#f1f5f9; border-radius:3px; height:14px; width:100%; position:relative;">
                        <div style="background:{{ $cBarColor }}; width:{{ $barW }}%; height:14px; border-radius:3px; display:inline-block;"></div>
                    </div>
                </td>
                <td style="border:none; border-bottom:1px solid #f0f0f0; padding:5px 6px; width:8%; text-align:center; vertical-align:middle;">
                    <span style="display:inline-block; background:{{ $cBadgeBg }}; color:{{ $cBadgeColor }}; border:1px solid {{ $cBadgeBorder }}; padding:1px 5px; border-radius:3px; font-size:9px; font-weight:900;">{{ $cPct }}%</span>
                </td>
                <td style="border:none; border-bottom:1px solid #f0f0f0; padding:5px 6px; width:8%; text-align:center; font-weight:900; font-size:11px; color:{{ $cBadgeColor }}; vertical-align:middle;">
                    @if($res['scale_type'] === 'yes_no') {{ $cAvg }}% @else {{ $cAvg }}/{{ $cMax }} @endif
                </td>
            </tr>
            @endforeach
        </table>

        {{-- Garis rata-rata --}}
        @if($overallAvg ?? false)
        <div style="margin-top:12px; background:#eef2ff; border:1px solid #c7d2fe; border-radius:4px; padding:8px 12px;">
            <table style="width:100%; border:none; border-collapse:collapse;">
                <tr>
                    <td style="border:none; padding:2px 6px; font-size:10px; font-weight:900; color:#3730a3;">&#9658; RATA-RATA KESELURUHAN SURVEI</td>
                    <td style="border:none; padding:2px 6px; text-align:right; font-size:13px; font-weight:900; color:#1e1b4b;">
                        {{ $overallAvg }} / 5.0 &nbsp;|&nbsp; Indeks: <span style="color:{{ $aiColor ?? '#4f46e5' }}">{{ $overallPct }}%</span>
                    </td>
                </tr>
            </table>
        </div>
        @endif
    </div>
    @endif


    {{-- ====== SECTION F: DETAIL LEMBAR EVALUASI INDIVIDU ====== --}}
    @if(isset($individualResponses) && $individualResponses->count() > 0)
    <div class="page-break"></div>

    <div class="section-title">
        F. Detail Lembar Evaluasi Individu (Lengkap)
        <span class="section-subtitle">&mdash; Rincian jawaban setiap responden per butir pertanyaan</span>
    </div>
    <div style="border:1px solid #c7d2fe; border-top:none; background:#f8f9ff; padding:8px 12px; margin-bottom:16px;">
        <p style="font-size:10px; color:#374151; margin:0;">
            Bagian ini memuat lembar evaluasi lengkap masing-masing responden, mencakup jawaban pada setiap butir pertanyaan, skor, kategori, serta rekomendasi kebijakan individual berdasarkan hasil analisis sistem.
            Total responden yang dilaporkan: <strong>{{ $individualResponses->count() }} orang</strong>.
        </p>
    </div>

    @foreach($individualResponses as $rIdx => $resp)
    @php
        $rSc = is_numeric($resp->average_score) ? floatval($resp->average_score) : null;
        $rPct = $rSc ? round(($rSc / 5) * 100, 1) : 0;
        if (!$rSc) {
            $rCat = '-'; $rCatColor = '#6b7280'; $rCatBg = '#f9fafb'; $rCatBorder = '#e5e7eb';
        } elseif ($rPct >= 85) {
            $rCat = 'Sangat Baik (Excellence)'; $rCatColor = '#065f46'; $rCatBg = '#d1fae5'; $rCatBorder = '#6ee7b7';
        } elseif ($rPct >= 70) {
            $rCat = 'Baik (Good)'; $rCatColor = '#1e40af'; $rCatBg = '#dbeafe'; $rCatBorder = '#93c5fd';
        } elseif ($rPct >= 50) {
            $rCat = 'Cukup (Moderate)'; $rCatColor = '#92400e'; $rCatBg = '#fef3c7'; $rCatBorder = '#fbbf24';
        } else {
            $rCat = 'Di Bawah Standar (Poor)'; $rCatColor = '#991b1b'; $rCatBg = '#fee2e2'; $rCatBorder = '#fca5a5';
        }

        // AI recommendation per individual
        if (!$rSc) {
            $rAiRec = 'Belum cukup data skor untuk dianalisis. Responden mungkin hanya mengisi pertanyaan esai.';
        } elseif ($rPct >= 85) {
            $rAiRec = 'Responden ini menunjukkan tingkat keselarasan sangat tinggi (Excellence) dengan standar institusi. Sangat direkomendasikan sebagai Role Model / Mentor bagi rekan sejawat. Pertimbangkan promosi peran strategis atau penugasan khusus sebagai fasilitator program pelatihan internal.';
        } elseif ($rPct >= 70) {
            $rAiRec = 'Responden tergolong kompeten (Good) dan menjalankan perannya dengan baik. Masih terdapat ruang penguatan di beberapa sub-kompetensi. Disarankan untuk mengikuti pelatihan tingkat lanjut atau mendapat delegasi tugas yang lebih menantang (project-based assignment).';
        } elseif ($rPct >= 50) {
            $rAiRec = 'Responden berada pada kategori menengah (Moderate). Terdeteksi adanya stagnasi performa atau kurangnya pemahaman terhadap kebijakan terbaru. Diperlukan pendampingan terarah dan program penyegaran (refresher course). Pemantauan berkala oleh atasan sangat dianjurkan.';
        } else {
            $rAiRec = 'PERINGATAN: Evaluasi responden ini berada di bawah standar kepatuhan Yayasan (Poor). Segera jadwalkan sesi konseling 1-on-1 atau mediasi formal. Diperlukan asesmen mendalam untuk menentukan rencana pembinaan intensif atau peninjauan ulang status yang bersangkutan.';
        }

        $sortedAnswers = $resp->answers->sortBy(fn($a) => optional($a->question)->order ?? 999);
        $scaleAnswers = $sortedAnswers->filter(fn($a) => optional($a->question)->type === 'scale');
        $textAnswers  = $sortedAnswers->filter(fn($a) => optional($a->question)->type === 'text');
    @endphp

    {{-- Page break before each respondent (except first) --}}
    @if($rIdx > 0)
    <div class="page-break"></div>
    @endif

    {{-- ===== HEADER KARTU RESPONDEN ===== --}}
    <div style="background:#1e1b4b; color:#fff; padding:10px 14px; border-radius:4px 4px 0 0; margin-top:10px;">
        <table style="width:100%; border:none; border-collapse:collapse;">
            <tr>
                <td style="border:none; padding:2px 4px; vertical-align:middle; width:60%;">
                    <div style="font-size:9px; color:#a5b4fc; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:3px;">
                        Lembar Evaluasi #{{ $rIdx + 1 }} dari {{ $individualResponses->count() }}
                    </div>
                    <div style="font-size:15px; font-weight:900; color:#fff;">{{ $resp->user ? $resp->user->name : 'Anonim' }}</div>
                    <div style="font-size:10px; color:#c7d2fe; margin-top:2px;">
                        {{ $resp->user && $resp->user->email ? $resp->user->email : '-' }}
                        @if($resp->teacher_type) &nbsp;|&nbsp; Guru {{ ucfirst($resp->teacher_type) }} @endif
                    </div>
                </td>
                <td style="border:none; padding:2px 4px; vertical-align:middle; text-align:right; width:40%;">
                    <div style="font-size:9px; color:#a5b4fc; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:3px;">Skor Akhir</div>
                    <div style="font-size:26px; font-weight:900; color:#fff;">
                        {{ $resp->average_score }}
                        @if($rSc)<span style="font-size:12px; font-weight:600; color:#a5b4fc;"> / 5.0</span>@endif
                    </div>
                    <div style="display:inline-block; background:{{ $rCatBg }}; color:{{ $rCatColor }}; border:1px solid {{ $rCatBorder }}; padding:2px 8px; border-radius:3px; font-size:9px; font-weight:900; margin-top:3px;">
                        {{ $rCat }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ===== TABEL JAWABAN SKALA ===== --}}
    @if($scaleAnswers->count() > 0)
    <table style="width:100%; border-collapse:collapse; border:1px solid #c7d2fe; border-top:none; margin-bottom:0;">
        <thead>
            <tr style="background:#4f46e5;">
                <th style="border:1px solid #4338ca; padding:6px 8px; color:#fff; font-size:10px; text-align:center; width:5%;">No</th>
                <th style="border:1px solid #4338ca; padding:6px 8px; color:#fff; font-size:10px; text-align:left; width:55%;">Butir Pertanyaan</th>
                <th style="border:1px solid #4338ca; padding:6px 8px; color:#fff; font-size:10px; text-align:center; width:12%;">Tipe Skala</th>
                <th style="border:1px solid #4338ca; padding:6px 8px; color:#fff; font-size:10px; text-align:center; width:10%;">Skor</th>
                <th style="border:1px solid #4338ca; padding:6px 8px; color:#fff; font-size:10px; text-align:left; width:18%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($scaleAnswers as $aIdx => $ans)
            @php
                $q = $ans->question;
                $rating = $ans->rating;
                $st = optional($q)->scale_type ?? 'likert_5';
                $maxR = $st === 'likert_4' ? 4 : ($st === 'yes_no' ? 1 : 5);

                if ($st === 'yes_no') {
                    $ratingLabel = $rating == 1 ? 'Ya / Setuju' : 'Tidak / Kurang Setuju';
                    $ratingColor = $rating == 1 ? '#065f46' : '#991b1b';
                    $ratingBg    = $rating == 1 ? '#d1fae5' : '#fee2e2';
                } elseif ($st === 'competence_5') {
                    $labels = [1=>'Sangat Kurang',2=>'Kurang Menguasai',3=>'Cukup Menguasai',4=>'Menguasai/Baik',5=>'Sangat Menguasai'];
                    $ratingLabel = $labels[$rating] ?? '-';
                    $ratingColor = $rating >= 4 ? '#065f46' : ($rating == 3 ? '#92400e' : '#991b1b');
                    $ratingBg    = $rating >= 4 ? '#d1fae5' : ($rating == 3 ? '#fef3c7' : '#fee2e2');
                } elseif ($st === 'likert_4') {
                    $labels = [1=>'Sangat Tidak Setuju',2=>'Tidak Setuju',3=>'Setuju',4=>'Sangat Setuju'];
                    $ratingLabel = $labels[$rating] ?? '-';
                    $ratingColor = $rating >= 3 ? '#1e40af' : ($rating == 2 ? '#92400e' : '#991b1b');
                    $ratingBg    = $rating >= 3 ? '#dbeafe' : ($rating == 2 ? '#fef3c7' : '#fee2e2');
                } else {
                    $labels = [1=>'Sangat Tidak Setuju',2=>'Tidak Setuju',3=>'Ragu-ragu/Netral',4=>'Setuju',5=>'Sangat Setuju'];
                    $ratingLabel = $labels[$rating] ?? '-';
                    $ratingColor = $rating >= 4 ? '#065f46' : ($rating == 3 ? '#1e40af' : ($rating == 2 ? '#92400e' : '#991b1b'));
                    $ratingBg    = $rating >= 4 ? '#d1fae5' : ($rating == 3 ? '#dbeafe' : ($rating == 2 ? '#fef3c7' : '#fee2e2'));
                }
                $rowBg = $aIdx % 2 === 0 ? '#ffffff' : '#f5f7ff';
            @endphp
            <tr style="background:{{ $rowBg }};">
                <td style="border:1px solid #dde3ff; padding:6px 8px; text-align:center; font-weight:900; color:#4f46e5; font-size:11px;">{{ optional($q)->order ?? ($aIdx+1) }}</td>
                <td style="border:1px solid #dde3ff; padding:6px 8px; font-size:10px; font-weight:600; color:#1e293b;">{{ optional($q)->question_text ?? '-' }}</td>
                <td style="border:1px solid #dde3ff; padding:6px 8px; text-align:center; font-size:9px; color:#6366f1; font-weight:700;">{{ str_replace('_',' ',strtoupper($st)) }}</td>
                <td style="border:1px solid #dde3ff; padding:6px 8px; text-align:center; font-weight:900; font-size:14px; color:{{ $ratingColor }};">
                    {{ $st === 'yes_no' ? ($rating == 1 ? 'Ya' : 'Tidak') : $rating }}
                    @if($st !== 'yes_no')<span style="font-size:9px; color:#9ca3af;"> /{{ $maxR }}</span>@endif
                </td>
                <td style="border:1px solid #dde3ff; padding:5px 8px;">
                    <span style="display:inline-block; background:{{ $ratingBg }}; color:{{ $ratingColor }}; border:1px solid {{ $ratingColor }}; padding:2px 6px; border-radius:3px; font-size:9px; font-weight:700;">{{ $ratingLabel }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background:#eef2ff;">
                <td colspan="3" style="border:1px solid #c7d2fe; padding:7px 10px; font-size:10px; font-weight:900; color:#3730a3; text-align:right;">SKOR RATA-RATA KESELURUHAN:</td>
                <td style="border:1px solid #c7d2fe; padding:7px 8px; text-align:center; font-weight:900; font-size:14px; color:#1e1b4b;">
                    {{ $resp->average_score }}{{ $rSc ? ' / 5.0' : '' }}
                </td>
                <td style="border:1px solid #c7d2fe; padding:7px 8px;">
                    @if($rCat !== '-')<span style="display:inline-block; background:{{ $rCatBg }}; color:{{ $rCatColor }}; border:1px solid {{ $rCatBorder }}; padding:2px 6px; border-radius:3px; font-size:9px; font-weight:700;">{{ $rCat }}</span>@endif
                </td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- ===== JAWABAN ESAI ===== --}}
    @if($textAnswers->count() > 0)
    <div style="background:#374151; color:#fff; padding:5px 10px; font-size:10px; font-weight:bold; margin-top:8px;">
        Jawaban Esai / Teks Terbuka
    </div>
    <div style="border:1px solid #d1d5db; border-top:none; margin-bottom:8px;">
        @foreach($textAnswers as $tAns)
        @php $tQ = $tAns->question; @endphp
        <div style="padding:7px 10px; border-bottom:1px dashed #e5e7eb;">
            <div style="font-size:10px; font-weight:900; color:#374151; margin-bottom:3px;">
                P{{ optional($tQ)->order ?? '?' }}: {{ optional($tQ)->question_text ?? 'Pertanyaan Esai' }}
                @if(!is_null($tAns->essay_score))
                    <span style="display:inline-block; background:#15803d; color:#fff; padding:1px 6px; border-radius:2px; font-size:9px; margin-left:6px;">Nilai: {{ $tAns->essay_score }}/5</span>
                @endif
            </div>
            <div style="font-size:11px; color:#374151; font-style:italic; padding-left:8px;">
                "{{ $tAns->answer_text ?? '(tidak diisi)' }}"
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ===== AI INSIGHT INDIVIDU ===== --}}
    <div style="background:{{ $rCatBg }}; border:1px solid {{ $rCatBorder }}; border-radius:0 0 4px 4px; padding:8px 12px; margin-bottom:4px;">
        <table style="width:100%; border:none; border-collapse:collapse;">
            <tr>
                <td style="border:none; padding:2px 6px; width:18px; font-size:14px; color:{{ $rCatColor }}; vertical-align:top;">&#129302;</td>
                <td style="border:none; padding:2px 6px; vertical-align:top;">
                    <strong style="font-size:10px; color:{{ $rCatColor }}; text-transform:uppercase; letter-spacing:0.5px;">Rekomendasi Kebijakan AI &mdash; {{ $resp->user ? $resp->user->name : 'Anonim' }}</strong><br>
                    <span style="font-size:10px; color:#1f2937; line-height:1.6;">{{ $rAiRec }}</span>
                </td>
            </tr>
        </table>
    </div>

    @endforeach
    @endif

    {{-- ====== EXECUTIVE RESUME ====== --}}
    <div class="page-break"></div>

    <div class="section-title" style="background:#0f172a; border-radius:4px 4px 0 0;">
        &#9733; RESUME EKSEKUTIF
        <span class="section-subtitle">&mdash; Ringkasan hasil survei untuk pimpinan &amp; pengambil keputusan</span>
    </div>
    <div style="border:1px solid #334155; border-top:none; padding:16px; margin-bottom:12px; background:#f8fafc;">

        {{-- Baris 1: Statistik Kunci --}}
        <table style="width:100%; border-collapse:collapse; margin-bottom:14px;">
            <tr>
                <td style="border:1px solid #e2e8f0; padding:10px 12px; background:#fff; text-align:center; width:25%; border-radius:4px;">
                    <div style="font-size:8px; font-weight:900; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; margin-bottom:4px;">Total Responden</div>
                    <div style="font-size:28px; font-weight:900; color:#1e1b4b;">{{ $totalResponses }}</div>
                    <div style="font-size:9px; color:#94a3b8; margin-top:2px;">
                        @if(isset($totalTargetUsers) && $totalTargetUsers > 0) dari {{ $totalTargetUsers }} populasi @endif
                    </div>
                </td>
                <td style="border:none; width:2%;"></td>
                <td style="border:1px solid #e2e8f0; padding:10px 12px; background:#fff; text-align:center; width:25%; border-radius:4px;">
                    <div style="font-size:8px; font-weight:900; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; margin-bottom:4px;">Rata-rata Skor</div>
                    <div style="font-size:28px; font-weight:900; color:{{ $aiColor ?? '#4f46e5' }};">{{ $overallAvg ?? '-' }}</div>
                    <div style="font-size:9px; color:#94a3b8; margin-top:2px;">dari skala 5.0</div>
                </td>
                <td style="border:none; width:2%;"></td>
                <td style="border:1px solid #e2e8f0; padding:10px 12px; background:#fff; text-align:center; width:25%; border-radius:4px;">
                    <div style="font-size:8px; font-weight:900; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; margin-bottom:4px;">Indeks Kepuasan</div>
                    <div style="font-size:28px; font-weight:900; color:{{ $aiColor ?? '#4f46e5' }};">{{ $overallPct ?? '-' }}%</div>
                    <div style="font-size:9px; color:#94a3b8; margin-top:2px;">dari 100%</div>
                </td>
                <td style="border:none; width:2%;"></td>
                <td style="border:1px solid {{ $aiBorder ?? '#c7d2fe' }}; padding:10px 12px; background:{{ $aiBg ?? '#eef2ff' }}; text-align:center; width:21%; border-radius:4px;">
                    <div style="font-size:8px; font-weight:900; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; margin-bottom:4px;">Kategori Capaian</div>
                    <div style="font-size:11px; font-weight:900; color:{{ $aiColor ?? '#4f46e5' }}; line-height:1.3;">{{ $aiTier ?? '-' }}</div>
                </td>
            </tr>
        </table>

        {{-- Baris 2: Top 3 Terkuat & Terlemah --}}
        @if($scaleResults->count() > 0)
        @php
            $sortedByScore = $scaleResults
                ->filter(fn($r) => $r['scale_type'] !== 'yes_no' && $r['total_answers'] > 0)
                ->sortByDesc('average')
                ->values();
            $top3 = $sortedByScore->take(3);
            $bottom3 = $sortedByScore->reverse()->take(3)->reverse()->values();
        @endphp
        <table style="width:100%; border-collapse:collapse; margin-bottom:14px;">
            <tr>
                <td style="border:none; width:49%; vertical-align:top;">
                    <div style="background:#d1fae5; border:1px solid #6ee7b7; border-radius:4px; padding:10px 12px;">
                        <div style="font-size:10px; font-weight:900; color:#065f46; margin-bottom:8px;">&#128200; 3 Indikator Terkuat</div>
                        @foreach($top3 as $ti => $tr)
                        <table style="width:100%; border:none; border-collapse:collapse; margin-bottom:5px;">
                            <tr>
                                <td style="border:none; padding:0 4px; width:18px; font-size:9px; font-weight:900; color:#065f46; vertical-align:middle;">{{ $ti+1 }}.</td>
                                <td style="border:none; padding:0 4px; font-size:9px; color:#1f2937; font-weight:600; vertical-align:middle; line-height:1.3;">{{ mb_substr($tr['question']->question_text, 0, 60) }}{{ mb_strlen($tr['question']->question_text) > 60 ? '...' : '' }}</td>
                                <td style="border:none; padding:0 4px; width:40px; text-align:right; font-size:10px; font-weight:900; color:#065f46; vertical-align:middle;">{{ $tr['average'] }}/5</td>
                            </tr>
                        </table>
                        @endforeach
                    </div>
                </td>
                <td style="border:none; width:2%;"></td>
                <td style="border:none; width:49%; vertical-align:top;">
                    <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:4px; padding:10px 12px;">
                        <div style="font-size:10px; font-weight:900; color:#991b1b; margin-bottom:8px;">&#128201; 3 Indikator Perlu Perhatian</div>
                        @foreach($bottom3 as $bi => $br)
                        <table style="width:100%; border:none; border-collapse:collapse; margin-bottom:5px;">
                            <tr>
                                <td style="border:none; padding:0 4px; width:18px; font-size:9px; font-weight:900; color:#991b1b; vertical-align:middle;">{{ $bi+1 }}.</td>
                                <td style="border:none; padding:0 4px; font-size:9px; color:#1f2937; font-weight:600; vertical-align:middle; line-height:1.3;">{{ mb_substr($br['question']->question_text, 0, 60) }}{{ mb_strlen($br['question']->question_text) > 60 ? '...' : '' }}</td>
                                <td style="border:none; padding:0 4px; width:40px; text-align:right; font-size:10px; font-weight:900; color:#991b1b; vertical-align:middle;">{{ $br['average'] }}/5</td>
                            </tr>
                        </table>
                        @endforeach
                    </div>
                </td>
            </tr>
        </table>
        @endif

        {{-- Narasi Ringkasan Eksekutif --}}
        <div style="background:#fff; border:1px solid #e2e8f0; border-left:4px solid {{ $aiColor ?? '#4f46e5' }}; border-radius:0 4px 4px 0; padding:10px 14px; margin-bottom:14px;">
            <div style="font-size:10px; font-weight:900; color:#0f172a; margin-bottom:6px;">&#128221; Narasi Ringkasan Eksekutif</div>
            <p style="font-size:11px; color:#374151; line-height:1.7; margin:0;">
                Berdasarkan hasil survei <strong>{{ $survey->title }}</strong> yang melibatkan <strong>{{ $totalResponses }} responden</strong>
                @if(isset($totalTargetUsers) && $totalTargetUsers > 0)
                    dari total populasi <strong>{{ $totalTargetUsers }}</strong> (tingkat partisipasi: <strong>{{ round(($totalResponses/$totalTargetUsers)*100, 1) }}%</strong>)
                @endif
                , diperoleh skor rata-rata keseluruhan sebesar <strong>{{ $overallAvg ?? '-' }} / 5.0</strong> dengan indeks kepuasan <strong>{{ $overallPct ?? '-' }}%</strong>.
                Kondisi ini menempatkan institusi pada kategori <strong>{{ $aiTier ?? '-' }}</strong>.
                {{ $aiNarrative ?? '' }}
            </p>
        </div>

        {{-- Rekomendasi Final --}}
        <div style="background:{{ $aiBg ?? '#eef2ff' }}; border:1px solid {{ $aiBorder ?? '#c7d2fe' }}; border-radius:4px; padding:10px 14px; margin-bottom:14px;">
            <div style="font-size:10px; font-weight:900; color:{{ $aiColor ?? '#4f46e5' }}; margin-bottom:6px;">&#127919; Rekomendasi Tindak Lanjut</div>
            <p style="font-size:11px; color:#374151; line-height:1.7; margin:0;">{{ $aiRecom ?? '-' }}</p>
        </div>

        {{-- Tabel Persetujuan Eksekutif --}}
        <div style="margin-top:4px;">
            <div style="font-size:10px; font-weight:900; color:#0f172a; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.5px;">&#9999; Lembar Persetujuan Eksekutif</div>
            <table style="width:100%; border-collapse:collapse; border:1px solid #334155;">
                <thead>
                    <tr style="background:#0f172a; color:#fff;">
                        <th style="border:1px solid #334155; padding:7px 10px; font-size:10px; text-align:center; width:33%;">Kepala Sekolah</th>
                        <th style="border:1px solid #334155; padding:7px 10px; font-size:10px; text-align:center; width:33%;">Pengawas / Supervisor Yayasan</th>
                        <th style="border:1px solid #334155; padding:7px 10px; font-size:10px; text-align:center; width:34%;">Pimpinan Yayasan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid #334155; padding:50px 10px 8px; text-align:center; font-size:10px; color:#64748b;">
                            Nama: .................................<br>
                            Tanda Tangan: .......................
                        </td>
                        <td style="border:1px solid #334155; padding:50px 10px 8px; text-align:center; font-size:10px; color:#64748b;">
                            Nama: .................................<br>
                            Tanda Tangan: .......................
                        </td>
                        <td style="border:1px solid #334155; padding:50px 10px 8px; text-align:center; font-size:10px; color:#64748b;">
                            Nama: .................................<br>
                            Tanda Tangan: .......................
                        </td>
                    </tr>
                    <tr style="background:#f8fafc;">
                        <td colspan="3" style="border:1px solid #334155; padding:6px 10px; font-size:10px; color:#64748b; text-align:center;">
                            Tanggal Pengesahan: {{ now()->format('d M Y') }} &nbsp;|&nbsp; Dokumen No: PEMBDA/SURVEI/{{ $survey->id }}/{{ now()->format('Y') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer">
        Laporan ini dicetak secara otomatis oleh sistem <strong>PembdaHUB</strong> pada tanggal {{ now()->format('d M Y, H:i') }} WIB.
        Dokumen ini bersifat rahasia dan hanya untuk keperluan evaluasi internal Perguruan PEMBDA.
    </div>

</body>
</html>
