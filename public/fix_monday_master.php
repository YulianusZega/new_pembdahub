<?php
$file = __DIR__.'/full_auto_plot.php';
$content = file_get_contents($file);

// We will use regex to completely replace the monday arrays.
function replace_monday($className, $replacement, $content) {
    // Find the class block
    $pattern = "/('$className'\s*=>\s*\[\s*)(.*?)(\s*\]\s*,?\s*'(?:X|XI|XII) )/is";
    return preg_replace_callback($pattern, function($m) use ($replacement) {
        $inner = $m[2];
        // Remove all monday entries from inner
        $inner = preg_replace("/\['day'=>'monday'.*?\],?/", "", $inner);
        // Add new monday entries at the beginning
        $inner = $replacement . "," . trim($inner, " ,\n\r\t");
        return $m[1] . $inner . $m[3];
    }, $content);
}

// X DPIB: 2-4 PAN C (Y. Ndraha), 5-7 B.IND (Ester Tel)
$content = replace_monday('X DPIB', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all']", $content);

// X TE: 2-4 PAN C (Y. Ndraha), 5-7 B.IND (Ester Tel)
$content = replace_monday('X TE', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all']", $content);

// X TKR.2: 2-4 PAN C (Y. Ndraha), 5-7 B.IND (Ester Tel)
$content = replace_monday('X TKR.2', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all']", $content);

// X TKR.1: 2-4 B.ING (Oti Laoli), 5-7 B.ING(split)/PAN C(split)
$content = replace_monday('X TKR.1', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'split']", $content);

// X TSM.1: 2-4 MUL OK (Markus Zeb), 5-7 AGM (Ofer Zega)
$content = replace_monday('X TSM.1', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all']", $content);

// X TSM.2: 2-4 SBD (Ester Tel), 5-6 MUL OK (Markus Zeb split) / KKA (Yamo Tel split) -> Wait, in PDF 5-7 is MUL OK/KKA. Let's make it 5-7.
$content = replace_monday('X TSM.2', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'SBD', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KKA', 'guru'=>'Yamo Tel', 'tipe'=>'split']", $content);

// X ACP: 2-4 PIPAS (Elven Nehe), 5-7 PIPAS (Elven Nehe)
$content = replace_monday('X ACP', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all']", $content);

// X TKJ Industri: 2-4 PJOK (Tn Hal), 5-7 BK (Firwanus split) / MUL OK (Markus split)
$content = replace_monday('X TKJ Industri', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Firwanus Zg', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split']", $content);

// XI DPIB: 2-4 MTK (Adis Zai), 5-7 MUL OK(Markus split) / BK(Nofika split)
$content = replace_monday('XI DPIB', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'split']", $content);

// XI TE: 2-4 MTK (Adis Zai), 5-7 MUL OK(Markus split) / BK(Nofika split)
$content = replace_monday('XI TE', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'split']", $content);

// XI TKR 1: 2-4 B.IND (Hilda Hulu), 5-7 BK (Nofika)
$content = replace_monday('XI TKR 1', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all']", $content);

// XI TKR 2: 2-4 AGM (Ofer Zega), 5-6 B.ING (Immel Tel), 7 MUL OK (Fider Har)
$content = replace_monday('XI TKR 2', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>6, 'mapel'=>'B.ING', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'monday', 'start'=>7, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all']", $content);

// XI TSM 1: 2-4 PSS (Immel Tel), 5-7 MTK (Adis Zai)
$content = replace_monday('XI TSM 1', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PSS', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all']", $content);

// XI TSM 2: 2-4 AGM (Jul Taf), 5-7 KIK (Arlika Zeb)
$content = replace_monday('XI TSM 2', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KIK', 'guru'=>'Arlika Zeb', 'tipe'=>'all']", $content);

// XI ACP: 2-3 BK (Nofika), 4-7 KIK (Fidel Har) - Wait, PDF says 4 is KIK, 5-7 is KIK. So 4-7 KIK.
$content = replace_monday('XI ACP', "['day'=>'monday', 'start'=>2, 'end'=>3, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'monday', 'start'=>4, 'end'=>7, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all']", $content);

// XI TKJ: 2-4 MUL OK (Fider Har), 5-7 AGM (Jul Taf)
$content = replace_monday('XI TKJ', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all']", $content);

// XII TE: 2-4 KK-TE (Fil Hulu), 5-7 KK-TE (Fil Hulu)
$content = replace_monday('XII TE', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TE', 'guru'=>'Fil Hulu', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TE', 'guru'=>'Fil Hulu', 'tipe'=>'all']", $content);

// XII TKJ: 2-4 KK-TKJ (Erwin Mend), 5-7 KK-TKJ (Erwin Mend)
$content = replace_monday('XII TKJ', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all']", $content);

// XII TKR INDUSTRI: 2-4 KK-TKR (Peniel Zeb), 5-7 KK-TKR (Peniel Zeb)
$content = replace_monday('XII TKR INDUSTRI', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all']", $content);

// XII TKR 2: 2-4 KK-TKR (Peniel Zeb), 5-7 KK-TKR (Peniel Zeb)
$content = replace_monday('XII TKR 2', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all']", $content);

// XII TSM 1: 2-4 KK-TSM (Defe Har), 5-7 KK-TSM (Defe Har)
$content = replace_monday('XII TSM 1', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all']", $content);

// XII TSM 2: 2-4 KK-TSM (Defe Har), 5-7 KK-TSM (Defe Har)
$content = replace_monday('XII TSM 2', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all']", $content);

// XII ACP: 2-4 KK-TKJ (Devi Hal), 5-7 KK-TKJ (Devi Hal)
$content = replace_monday('XII ACP', "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKJ', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKJ', 'guru'=>'Devi Hal', 'tipe'=>'all']", $content);

// XII DPIB: 2-4 KK-DPIB (Resman Har), 5-7 KK-DPIB (Resman Har)
$content = preg_replace_callback("/('XII DPIB'\s*=>\s*\[\s*)(.*?)(\s*\]\s*;)/is", function($m) {
    $inner = $m[2];
    $inner = preg_replace("/\['day'=>'monday'.*?\],?/", "", $inner);
    $inner = "['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-DPIB', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-DPIB', 'guru'=>'Resman Har', 'tipe'=>'all']," . trim($inner, " ,\n\r\t");
    return $m[1] . $inner . $m[3];
}, $content);

file_put_contents($file, $content);
echo "All Mondays fixed accurately!\n";
