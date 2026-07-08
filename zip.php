<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/deploy_profile.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $zip->addFile(__DIR__ . '/routes/web.php', 'routes/web.php');
    $zip->addFile(__DIR__ . '/app/Http/Controllers/ProfileSettingsController.php', 'app/Http/Controllers/ProfileSettingsController.php');
    $zip->addFile(__DIR__ . '/resources/views/profile/settings.blade.php', 'resources/views/profile/settings.blade.php');
    $zip->close();
    echo "ZIP_CREATED_SUCCESSFULLY\n";
} else {
    echo "FAILED_TO_CREATE_ZIP\n";
}
