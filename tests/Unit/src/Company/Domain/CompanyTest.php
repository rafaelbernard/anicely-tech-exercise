<?php

declare(strict_types=1);

namespace Tests\Unit\Company\Domain;

use App\Company\Domain\Company;
use App\Company\Domain\CompanyId;
use PHPUnit\Framework\TestCase;

final class CompanyTest extends TestCase
{
    public function testConstruction(): void
    {
        $id = new CompanyId(1);
        $company = new Company($id, 'ACME Corporation');
        
        $this->assertSame($id, $company->id);
        $this->assertEquals('ACME Corporation', $company->name);
    }

    public function testFromDbRow(): void
    {
        $row = [
            'id' => '42',
            'name' => 'Tech Solutions Inc'
        ];
        
        $company = Company::fromDbRow($row);
        
        $this->assertEquals(42, $company->id->value);
        $this->assertEquals('Tech Solutions Inc', $company->name);
    }

    public function testFromFkDbRow(): void
    {
        $row = [
            'company_id' => '15',
            'company_name' => 'Global Industries'
        ];
        
        $company = Company::fromFkDbRow($row);
        
        $this->assertEquals(15, $company->id->value);
        $this->assertEquals('Global Industries', $company->name);
    }
}