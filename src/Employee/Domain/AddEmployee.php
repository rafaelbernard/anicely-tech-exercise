<?php

declare(strict_types=1);

namespace App\Employee\Domain;

use App\Company\Domain\CompanyId;
use App\Core\Domain\Email;

final readonly class AddEmployee
{
    public function __construct(
        public CompanyId $companyId,
        public string $employeeName,
        public Email $email,
        public Money $salary
    ) {}
}
