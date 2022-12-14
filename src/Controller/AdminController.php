<?php

namespace App\Controller;

use App\Entity\Livres;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/tableau-de-bord', name: 'show_dashboard', methods: ['GET'])]
    public function showDashboard(): Response
    {
        return $this->render('admin/show_dashboard.html.twig');
    }

    #[Route('/liste-des-livres', name: 'show_livres', methods: ['GET'])]
    public function showLivres(EntityManagerInterface $entityManager): Response
    {
        $livres = $entityManager->getRepository(Livres::class)->findAll();

        return $this->render('admin/livre/show_livres.html.twig', [
            'livres' => $livres
        ]);
    }
}
