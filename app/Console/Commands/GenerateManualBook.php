<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class GenerateManualBook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pembda:generate-manual-book';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the beautiful PDF version of the PembdaHub Manual Book';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembuatan Manual Book PDF...');

        try {
            // Render the Blade template to PDF
            $pdf = Pdf::loadView('manual_book_pdf');
            $pdf->setPaper('a4', 'portrait');
            $pdf->setWarnings(false); // Disable PHP warnings in pdf parsing
            
            // Set options if needed, e.g. remote files or custom font pathing
            $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->set_option('isRemoteEnabled', true);

            // Save to public directory
            $publicPath = public_path('MANUAL_BOOK_PEMBDAHUB.pdf');
            $pdf->save($publicPath);
            $this->info('Manual Book PDF disimpan di folder public: ' . $publicPath);

            // Also save a copy in the root directory for easy access
            $rootPath = base_path('MANUAL_BOOK_PEMBDAHUB.pdf');
            copy($publicPath, $rootPath);
            $this->info('Manual Book PDF disalin ke folder root: ' . $rootPath);

            $this->info('Sukses! Manual Book PembdaHub PDF berhasil dibuat.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Gagal membuat PDF: ' . $e->getMessage());
            Log::error('Gagal membuat Manual Book PDF: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
