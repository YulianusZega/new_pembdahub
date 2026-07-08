<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Centralized file upload validation and handling.
 * Use this trait in controllers that need to handle file uploads.
 */
trait ValidatesFileUpload
{
    /**
     * Allowed file types and their max sizes (in KB).
     */
    protected function getFileRules(): array
    {
        return [
            'photo' => [
                'max_size' => 2048, // 2MB
                'mimes' => ['jpg', 'jpeg', 'png', 'webp'],
                'mime_types' => ['image/jpeg', 'image/png', 'image/webp'],
            ],
            'document' => [
                'max_size' => 15360, // 15MB
                'mimes' => ['pdf', 'jpg', 'jpeg', 'png'],
                'mime_types' => ['application/pdf', 'image/jpeg', 'image/png'],
            ],
            'spreadsheet' => [
                'max_size' => 10240, // 10MB
                'mimes' => ['xlsx', 'xls', 'csv'],
                'mime_types' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'],
            ],
            'material' => [
                'max_size' => 20480, // 20MB
                'mimes' => ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'mp4', 'mp3'],
                'mime_types' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-powerpoint',
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'image/jpeg',
                    'image/png',
                    'video/mp4',
                    'audio/mpeg',
                ],
            ],
        ];
    }

    /**
     * Get Laravel validation rules for a file type.
     *
     * @param string $type  One of: photo, document, spreadsheet, material
     * @param bool $required  Whether the file is required
     * @return array
     */
    protected function fileValidationRules(string $type, bool $required = false): array
    {
        $rules = $this->getFileRules()[$type] ?? $this->getFileRules()['document'];

        $validationRules = [
            $required ? 'required' : 'nullable',
            'file',
            'mimes:' . implode(',', $rules['mimes']),
            'max:' . $rules['max_size'],
        ];

        return $validationRules;
    }

    /**
     * Store an uploaded file with standardized naming.
     *
     * @param UploadedFile $file
     * @param string $directory  Storage subdirectory (e.g., 'photos', 'documents')
     * @param string|null $prefix  Optional filename prefix
     * @param string $disk  Storage disk to use
     * @return string  The stored file path
     */
    protected function storeUploadedFile(
        UploadedFile $file,
        string $directory,
        ?string $prefix = null,
        string $disk = 'public'
    ): string {
        $filename = ($prefix ? $prefix . '_' : '') . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, $disk);
    }

    /**
     * Delete a previously stored file.
     *
     * @param string|null $path  The file path to delete
     * @param string $disk  Storage disk
     * @return bool
     */
    protected function deleteUploadedFile(?string $path, string $disk = 'public'): bool
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
