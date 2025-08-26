<?php

declare(strict_types=1);

namespace App\Company\Domain;

final readonly class Company
{
    # TODO: Add created and updated dates
    public function __construct(
        public CompanyId $id,
        public string $name
    ) {}

    public static function fromDbRow(array $row): Company
    {
        return new self(
            new CompanyId((int) $row['id']),
            $row['name']
        );
    }

    public static function fromFkDbRow(array $row): Company
    {
        return new self(
            new CompanyId((int) $row['company_id']),
            $row['company_name']
        );
    }
}
