<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Domain;

use App\Core\Domain\Email;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    public function testValidConstruction(): void
    {
        $email = new Email('test@example.com');
        
        $this->assertEquals('test@example.com', $email->value);
    }

    public function testValueMethod(): void
    {
        $email = new Email('user@domain.org');
        
        $this->assertEquals('user@domain.org', $email->value());
    }

    public function testEqualsReturnsTrueForSameEmail(): void
    {
        $email1 = new Email('same@example.com');
        $email2 = new Email('same@example.com');
        
        $this->assertTrue($email1->equals($email2));
    }

    public function testEqualsReturnsFalseForDifferentEmail(): void
    {
        $email1 = new Email('first@example.com');
        $email2 = new Email('second@example.com');
        
        $this->assertFalse($email1->equals($email2));
    }

    public function testInvalidEmailThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');
        
        new Email('invalid-email');
    }

    public function testEmptyEmailThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');
        
        new Email('');
    }

    public function testEmailWithoutDomainThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email address');
        
        new Email('user@');
    }
}