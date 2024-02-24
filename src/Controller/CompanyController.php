<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompanyController extends AbstractController
{
    #[Route('/api/companies', name: 'company_list', methods: ['GET'])]
    public function index(ManagerRegistry $doctrine ): JsonResponse
    {
        $companies = $doctrine->getRepository(Company::class)->findAll();
        return $this->json($companies);
    }

    #[Route('/api/companies/{id}', name: 'company_show', methods: ['GET'])]
    public function show(int $id, CompanyRepository $companyRepository): JsonResponse
    {
        $company = $companyRepository->find($id);

        if (!$company) {
            return new JsonResponse(['error' => 'Company not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($company);
    }

    #[Route('/api/companies', name: 'company_create', methods: ['POST'])]
    public function create(Request $request,ManagerRegistry $doctrine): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $company = new Company();
        $company->setName($data['name']);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($company);
        $entityManager->flush();

        return $this->json($company, Response::HTTP_CREATED);
    }

    #[Route('/api/companies/{id}', name: 'company_update', methods: ['PUT'])]
    public function update(Request $request, int $id, EntityManagerInterface $entityManager, CompanyRepository $companyRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);     
        $company = $companyRepository->find($id);
        if (!$company) {
            return new JsonResponse(['error' => 'Company not found'], Response::HTTP_NOT_FOUND);
        }
 
        $company->setName($data['name']);
        $entityManager->flush();

        return $this->json($company);
    }

    #[Route('/api/companies/{id}', name: 'company_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager, CompanyRepository $companyRepository): JsonResponse
    {

        $company = $companyRepository->find($id);

        if (!$company) {
            return new JsonResponse(['error' => 'Company not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Company deleted successfully'], Response::HTTP_OK);
    }

}
