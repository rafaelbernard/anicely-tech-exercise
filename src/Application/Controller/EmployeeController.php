<?php

declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Validator\GlobalFileDataCsvValidator;
use App\Core\Domain\Email;
use App\Employee\Domain\Application\UpdateEmployeeEmailUseCase;
use App\Employee\Domain\Application\UploadEmployeesFromCsvUseCase;
use App\Employee\Domain\EmployeeId;
use App\Employee\Domain\EmployeeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employees')]
final class EmployeeController extends AbstractController
{
    public function __construct(
        private readonly EmployeeRepository $repository,
        private readonly UpdateEmployeeEmailUseCase $updateEmailUseCase,
        private readonly UploadEmployeesFromCsvUseCase $uploadCsvUseCase,
        private readonly int $csvMaxSizeMB,
        private readonly GlobalFileDataCsvValidator $csvValidator
    ) {}

    # TODO: Create a CreateEmployee Use Case

    #[Route('', name: 'employee_list', methods: ['GET'])]
    public function list(): Response
    {
        # TODO: Add pagination
        return $this->render('employee/list.html.twig', [
            'employees' => $this->repository->findAllWithCompanies()
        ]);
    }

    #[Route('/{id}/delete', name: 'employee_delete', methods: ['POST'])]
    public function delete(int $id): Response
    {
        $this->repository->delete(new EmployeeId($id));
        $this->addFlash('success', 'Employee deleted successfully');
        return $this->redirectToRoute('employee_list');
    }

    #[Route('/{id}/update-email', name: 'employee_update_email', methods: ['POST'])]
    public function updateEmail(int $id, Request $request): JsonResponse
    {
        try {
            $email = new Email($request->request->get('email'));
            $this->updateEmailUseCase->execute($id, $email);

            # TODO: Show a successful message
            $this->addFlash('success', 'Email updated successfully');
            $this->addFlash('info', 'Please refresh the page to see the changes');
            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
        }
    }

    #[Route('/upload', name: 'employee_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Symfony provides a nice way to get the uploaded file -- Skipping now per requirements
            /** @var UploadedFile $file */
            $file = $request->files->get('csv_file');
            $fileData = $_FILES['csv_file'] ?? [];

            try {
                $this->csvValidator->validate($fileData);
                $result = $this->uploadCsvUseCase->execute(file_get_contents($fileData['tmp_name']));
                $message = "Processed {$result['processed']} employees";
                if ($result['skipped'] > 0) {
                    $message .= ", skipped {$result['skipped']} duplicates";
                }
                $this->addFlash('success', $message);
                
                if (!empty($result['errors'])) {
                    foreach ($result['errors'] as $error) {
                        $this->addFlash('warning', $error);
                    }
                }
                
                return $this->redirectToRoute('employee_list');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('employee/upload.html.twig', [
            'csvMaxSizeMB' => $this->csvMaxSizeMB
        ]);
    }
}
