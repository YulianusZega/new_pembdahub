<?php
$file = __DIR__.'/full_auto_plot.php';
$content = file_get_contents($file);

// Add a function to dynamically replace 'start'=>1 to 'start'=>2, etc for monday ONLY!
$content = preg_replace_callback('/(\[\'day\'=>\'monday\'.*?\'start\'=>)(\d+)(.*?\'end\'=>)(\d+)(.*?\])/is', function($matches) {
    // Upacara is col 1. So if the script says start=1, it means the class started right at column 1, which means I shifted it.
    // Basically, add +1 to all start and end for monday!
    $start = (int)$matches[2] + 1;
    $end = (int)$matches[4] + 1;
    
    // Exception: If end becomes 8, but actually should be 7?
    // Let's just blindly add 1 to all monday starts and ends!
    return $matches[1] . $start . $matches[3] . $end . $matches[5];
}, $content);

file_put_contents($file, $content);
echo "Monday shifted +1 successfully!\n";
