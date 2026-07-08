<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TrainingModule;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

echo "Starting training modules PDF generation...\n";

// Ensure storage directory exists
$dirPath = 'training-modules';
if (!Storage::disk('public')->exists($dirPath)) {
    Storage::disk('public')->makeDirectory($dirPath);
    echo "Created public storage directory: storage/app/public/training-modules\n";
}

$modules = TrainingModule::all();

foreach ($modules as $module) {
    echo "Processing Module ID {$module->id}: {$module->title}...\n";
    
    // Parse Markdown to HTML
    $htmlContent = Str::markdown($module->content);
    
    // Simple HTML template for PDF layout
    $htmlTemplate = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>' . htmlspecialchars($module->title) . '</title>
        <style>
            @page {
                margin: 40px 50px;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                line-height: 1.6;
                color: #2d3748;
            }
            h1 {
                font-size: 18px;
                color: #1a365d;
                border-bottom: 2px solid #2b6cb0;
                padding-bottom: 5px;
                margin-bottom: 15px;
                text-align: center;
                text-transform: uppercase;
            }
            h2 {
                font-size: 13px;
                color: #2b6cb0;
                margin-top: 20px;
                border-bottom: 1px solid #e2e8f0;
                padding-bottom: 3px;
            }
            h3 {
                font-size: 11px;
                color: #2d3748;
                margin-top: 12px;
                font-weight: bold;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
                margin-bottom: 15px;
            }
            table, th, td {
                border: 1px solid #cbd5e0;
            }
            th {
                background-color: #edf2f7;
                color: #2b6cb0;
                font-weight: bold;
                padding: 6px;
                text-align: left;
                font-size: 10px;
            }
            td {
                padding: 6px;
                vertical-align: top;
                font-size: 10px;
            }
            ul, ol {
                margin-top: 5px;
                margin-bottom: 10px;
                padding-left: 20px;
            }
            li {
                margin-bottom: 4px;
            }
            blockquote {
                background-color: #f7fafc;
                border-left: 4px solid #4299e1;
                margin: 10px 0;
                padding: 8px 12px;
                color: #4a5568;
            }
            hr {
                border: 0;
                border-top: 1px solid #e2e8f0;
                margin: 15px 0;
            }
            pre {
                background-color: #f7fafc;
                border: 1px solid #edf2f7;
                padding: 10px;
                font-family: Courier, monospace;
                white-space: pre-wrap;
                font-size: 9px;
            }
            .page-break {
                page-break-after: always;
            }
        </style>
    </head>
    <body>
        ' . $htmlContent . '
    </body>
    </html>
    ';

    $pdfFileName = $module->slug . '.pdf';
    $relativePdfPath = $dirPath . '/' . $pdfFileName;
    $absolutePdfPath = Storage::disk('public')->path($relativePdfPath);

    // Save PDF
    Pdf::loadHTML($htmlTemplate)
       ->setPaper('a4', 'portrait')
       ->save($absolutePdfPath);
       
    // Update training module
    $module->pdf_file = $relativePdfPath;
    $module->save();
    
    echo "Generated and saved: {$absolutePdfPath}\n";
}

echo "All training modules PDFs have been generated and database records updated!\n";
