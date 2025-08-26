<?php

declare(strict_types=1);

namespace App\Employee\Domain\Application;

use App\Core\Domain\Email;

interface UpdateEmployeeEmailUseCase
{
    public function execute(int $employeeId, Email $email): void;
}