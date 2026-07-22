<?php
$file = __DIR__.'/full_auto_plot.php';
$content = file_get_contents($file);

// Add a function to dynamically replace 'start'=>2 to 'start'=>1, etc for monday ONLY!
$content = preg_replace_callback('/(\[\'day\'=>\'monday\'.*?\'start\'=>)(\d+)(.*?\'end\'=>)(\d+)(.*?\])/is', function($matches) {
    // Revert the +1 shift by subtracting 1
    $start = (int)$matches[2] - 1;
    $end = (int)$matches[4] - 1;
    
    return $matches[1] . $start . $matches[3] . $end . $matches[5];
}, $content);

file_put_contents($file, $content);
echo "Monday shifted -1 successfully (Reverted)!\n";
