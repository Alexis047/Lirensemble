<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    #[Route('/mon-profil', name: 'show_profile', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('profil/show_profile.html.twig');
    }
}
