<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livres;
use App\Form\LivreFormType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LivreController extends AbstractController
{
    #[Route('/ajouter-un-livre', name: 'add_livre', methods: ['GET', 'POST'])]
    public function addLivre(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livres();
        // récupération du user pour ajout en tant que propriétaire
        $user = $this->getUser();

        $form = $this->createForm(LivreFormType::class, $livre)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livre->setCreatedAt(new Datetime());
            $livre->setUpdatedAt(new Datetime());
            $livre->setProprietaire($user);

            $entityManager->persist($livre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre livre a été ajouté avec succès !');
            return $this->redirectToRoute('show_profile');
        } //end if

        return $this->render('livre/form.html.twig', [
            'form' => $form->createView()
        ]);
    } // end function addLivre

    #[Route('/modifier-un-livre/{id}', name: 'update_livre', methods: ['GET', 'POST'])]
    public function updateLivre(Livres $livre, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreFormType::class, $livre)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livre->setUpdatedAt(new Datetime());

            $entityManager->persist($livre);
            $entityManager->flush();

            $this->addFlash('success', 'Votre livre a été modifié avec succès !');
            return $this->redirectToRoute('show_profile');
        } //end if

        return $this->render('livre/form.html.twig', [
            'form' => $form->createView(),
            'livre' => $livre
        ]);
    } // end function updateLivre

    #[Route('/supprimer-un-livre/{id}', name: 'hard_delete_livre', methods: ['GET'])]
    public function hardDeleteLivre(Livres $livre, LivresRepository $repository): RedirectResponse
    {
        $repository->remove($livre, true);

        $this->addFlash('success', "Le livre a bien été retiré !");
        return $this->redirectToRoute('show_profile');
    } // end function hardDeleteLivre()
}
