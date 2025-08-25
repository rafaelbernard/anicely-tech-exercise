<?php

declare(strict_types=1);

namespace App\Application\Validator;

readonly class GlobalFileDataCsvValidator
{
    public function __construct(private int $maxSizeMB) {}

    /**
     * Expects to validate data from $_FILES array
     * @param mixed $fileData
     * @return void
     */
    public function validate(mixed $fileData): void
    {
        if (empty($fileData['name']) || $fileData['error'] === UPLOAD_ERR_NO_FILE) {
            throw new \InvalidArgumentException('Please select a file to upload');
        }
        
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('File upload failed. Please try again.');
        }
        
        $extension = strtolower(pathinfo($fileData['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            throw new \InvalidArgumentException('Please upload a valid CSV file');
        }
        
        $maxSizeBytes = $this->maxSizeMB * 1024 * 1024;
        
        if ($fileData['size'] > $maxSizeBytes) {
            throw new \InvalidArgumentException("File size must be less than {$this->maxSizeMB}MB");
        }
    }
}
