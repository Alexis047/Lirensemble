<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livres;
use App\Form\LivreFormType;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivreController extends AbstractController
{
    #[Route('/ajouter-un-livre', name: 'add_livre', methods: ['GET', 'POST'])]
    public function addLivre(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livres();

        $form = $this->createForm(LivreFormType::class, $livre)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $livre->setCreatedAt(new Datetime());
            $livre->setUpdatedAt(new Datetime());

            $entityManager->persist($livre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre livre a été ajouté avec succès !');
            return $this->redirectToRoute('show_profile');
        } //end if

        return $this->render('livre/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
