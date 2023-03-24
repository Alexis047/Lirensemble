<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livres;
use App\Entity\Emprunt;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmpruntController extends AbstractController
{
    #[Route('/voir-mon-emprunt', name: 'show_emprunt', methods:['GET'])]
    public function showEmprunt(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $monEmprunt = $entityManager->getRepository(Emprunt::class)->findBy(array('emprunteur' => $user, 'deletedAt' => null));

        return $this->render('emprunt/show_emprunt.html.twig', [
            "monEmprunt" => $monEmprunt,
            
        ]);
    }

    #[Route('/valider-mon-emprunt/{id}', name: 'validate_emprunt', methods:['GET'])]
    public function validateEmprunt(EntityManagerInterface $entityManager, Livres $livres): Response
    {
        $user = $this->getUser();
        $monEmprunt = $entityManager->getRepository(Emprunt::class)->findBy(array('emprunteur' => $user, 'deletedAt' => null));

        if ($user === null) {
            $this->addFlash('error', 'Inscrivez-vous pour pouvoir emprunter un livre');
            return $this->redirectToRoute('user_register');
        }
        elseif (empty($monEmprunt)) {
            $livres->setEnPret(true);
            $emprunt = new Emprunt();
            $emprunt->setEmprunteur($user);
            $emprunt->setLivre($livres);
            $emprunt->setCreatedAt(new DateTime());
            $emprunt->setUpdatedAt(new DateTime());

            $entityManager->persist($emprunt, $livres);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez emprunté ce livre');
            return $this->redirectToRoute('default_home');
        } else {
            $this->addFlash('error', 'Vous ne pouvez emprunter qu\'un seul livre à la fois');
            return $this->redirectToRoute('default_home');
        }
    }
}
