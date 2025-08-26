<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Validator;

use App\Application\Validator\GlobalFileDataCsvValidator;
use PHPUnit\Framework\TestCase;

final class GlobalFileDataCsvValidatorTest extends TestCase
{
    public function testValidFilePassesValidation(): void
    {
        $this->expectNotToPerformAssertions(); // No exception thrown

        $validator = new GlobalFileDataCsvValidator(5);
        
        $fileData = [
            'name' => 'test.csv',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024 * 1024 // 1MB
        ];
        
        $validator->validate($fileData);
    }

    public function testEmptyFileNameThrowsException(): void
    {
        $validator = new GlobalFileDataCsvValidator(5);
        
        $fileData = [
            'name' => '',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please select a file to upload');
        
        $validator->validate($fileData);
    }

    public function testNoFileUploadedThrowsException(): void
    {
        $validator = new GlobalFileDataCsvValidator(5);
        
        $fileData = [
            'name' => 'test.csv',
            'error' => UPLOAD_ERR_NO_FILE,
            'size' => 0
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please select a file to upload');
        
        $validator->validate($fileData);
    }

    public function testUploadErrorThrowsException(): void
    {
        $validator = new GlobalFileDataCsvValidator(5);
        
        $fileData = [
            'name' => 'test.csv',
            'error' => UPLOAD_ERR_PARTIAL,
            'size' => 1024
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File upload failed. Please try again.');
        
        $validator->validate($fileData);
    }

    public function testNonCsvFileThrowsException(): void
    {
        $validator = new GlobalFileDataCsvValidator(5);
        
        $fileData = [
            'name' => 'test.txt',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please upload a valid CSV file');
        
        $validator->validate($fileData);
    }

    public function testFileTooLargeThrowsException(): void
    {
        $validator = new GlobalFileDataCsvValidator(1); // 1MB limit
        
        $fileData = [
            'name' => 'test.csv',
            'error' => UPLOAD_ERR_OK,
            'size' => 2 * 1024 * 1024 // 2MB
        ];
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File size must be less than 1MB');
        
        $validator->validate($fileData);
    }
}
