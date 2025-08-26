<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Validator;

use App\Application\Validator\UploadFileCsvValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class UploadFileCsvValidatorTest extends TestCase
{
    public function testValidFilePassesValidation(): void
    {
        $this->expectNotToPerformAssertions();

        $validator = new UploadFileCsvValidator(5);

        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('csv');
        $file->method('isValid')->willReturn(true);
        $file->method('getSize')->willReturn(1024 * 1024); // 1MB

        $validator->validate($file);
    }

    public function testNullFileThrowsException(): void
    {
        $validator = new UploadFileCsvValidator(5);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please select a file to upload');
        
        $validator->validate(null);
    }

    public function testNonCsvFileThrowsException(): void
    {
        $validator = new UploadFileCsvValidator(5);
        
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('txt');
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Please upload a valid CSV file');
        
        $validator->validate($file);
    }

    public function testInvalidFileThrowsException(): void
    {
        $validator = new UploadFileCsvValidator(5);
        
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('csv');
        $file->method('isValid')->willReturn(false);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File upload failed. Please try again.');
        
        $validator->validate($file);
    }

    public function testFileTooLargeThrowsException(): void
    {
        $validator = new UploadFileCsvValidator(1); // 1MB limit
        
        $file = $this->createMock(UploadedFile::class);
        $file->method('getClientOriginalExtension')->willReturn('csv');
        $file->method('isValid')->willReturn(true);
        $file->method('getSize')->willReturn(2 * 1024 * 1024); // 2MB
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File size must be less than 1MB');
        
        $validator->validate($file);
    }
}
