<?php

namespace App\Application\Controller;

use App\Company\Domain\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/companies')]
final class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyRepository $repository,
    ) {}

    #[Route('/', name: 'company_averages', methods: ['GET'])]
    public function companyAverages(): Response
    {
        return $this->render('employee/companies.html.twig', [
            'companies' => $this->repository->getCompanyAverageSalaries()
        ]);
    }
}
