<?php

declare(strict_types=1);

namespace App\Employee\Domain\Application;

interface UploadEmployeesFromCsvUseCase
{
    public function execute(string $csvContent): array;
}