<?php

namespace App\Controller;

use App\Entity\Livres;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'default_home', methods: ['GET'])]
    public function home(EntityManagerInterface $entityManager): Response
    {
        $livres = $entityManager->getRepository(Livres::class)
        ->findBy(array('enPret' => false, 'deletedAt' => null));

        return $this->render('default/home.html.twig', [
            "livres" => $livres
        ]);
    }
}
