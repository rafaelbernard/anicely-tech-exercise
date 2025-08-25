<?php

declare(strict_types=1);

namespace App\Application\Validator;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final readonly class UploadFileCsvValidator
{
    public function __construct(private int $maxSizeMB) {}

    public function validate(?UploadedFile $file): void
    {
        if (!$file) {
            throw new \InvalidArgumentException('Please select a file to upload');
        }
        
        if ($file->getClientOriginalExtension() !== 'csv') {
            throw new \InvalidArgumentException('Please upload a valid CSV file');
        }
        
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('File upload failed. Please try again.');
        }
        
        $maxSizeBytes = $this->maxSizeMB * 1024 * 1024;
        
        if ($file->getSize() > $maxSizeBytes) {
            throw new \InvalidArgumentException("File size must be less than {$this->maxSizeMB}MB");
        }
    }
}
