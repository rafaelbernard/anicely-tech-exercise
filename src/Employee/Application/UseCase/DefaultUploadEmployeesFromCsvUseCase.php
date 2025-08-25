<?php

declare(strict_types=1);

namespace App\Employee\Application\UseCase;

use App\Company\Domain\CompanyRepository;
use App\Core\Domain\Email;
use App\Employee\Domain\AddEmployee;
use App\Employee\Domain\Application\UploadEmployeesFromCsvUseCase;
use App\Employee\Domain\EmployeeRepository;
use App\Employee\Domain\Money;

final readonly class DefaultUploadEmployeesFromCsvUseCase implements UploadEmployeesFromCsvUseCase
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private CompanyRepository $companyRepository
    ) {}

    # TODO: Improve
    # Processing files incurs memory and CPU overhead, so we should avoid doing it in a web request
    # Even if we process in chunks to prevent loading everything in memory, we still need to process the whole file
    # Ideally we skip doing in the web request and do it in a background job
    public function execute(string $csvContent): array
    {
        $lines = str_getcsv($csvContent, separator: "\n", escape: "\\");
        $errors = [];
        $processed = 0;
        $skipped = 0;

        foreach ($lines as $index => $line) {
            # TODO: Add a parameter to identify if header is provided and if we need to skip it
            if ($index === 0) continue; // Skip header
            
            $data = str_getcsv($line, escape: "\\");
            
            if (count($data) !== 4) {
                $errors[] = "Line " . ($index + 1) . ": Invalid format";
                continue;
            }

            try {
                # TODO: Validate and sanitize data - XSS, SQL injection, Formulas, etc
                $employeeName = trim($data[1]);
                $emailAddress = trim($data[2]);
                
                if ($this->employeeRepository->existsByNameAndEmail($employeeName, $emailAddress)) {
                    $skipped++;
                    continue;
                }

                $companyName = trim($data[0]);
                $company = $this->companyRepository->findByName($companyName);
                
                if (!$company) {
                    $company = $this->companyRepository->insert($companyName);
                }

                $addEmployee = new AddEmployee(
                    $company->id,
                    $employeeName,
                    new Email($emailAddress),
                    new Money((float) $data[3])
                );
                
                $this->employeeRepository->add($addEmployee);
                $processed++;
            } catch (\Exception $e) {
                $errors[] = "Line " . ($index + 1) . ": " . $e->getMessage();
            }
        }

        return ['processed' => $processed, 'skipped' => $skipped, 'errors' => $errors];
    }
}