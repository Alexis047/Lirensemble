<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Livres;
use App\Repository\LivresRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/tableau-de-bord', name: 'show_dashboard', methods: ['GET'])]
    public function showDashboard(): Response
    {
        return $this->render('admin/show_dashboard.html.twig');
    } // end function showDashboard

    // Livres

    #[Route('/liste-des-livres', name: 'show_livres', methods: ['GET'])]
    public function showLivres(EntityManagerInterface $entityManager): Response
    {
        $livres = $entityManager->getRepository(Livres::class)->findby(['deletedAt' => null]);

        return $this->render('admin/livre/show_livres.html.twig', [
            'livres' => $livres
        ]);
    } // end function showLivres

    #[Route('/archiver-un-livre/{id}', name: 'soft_delete_livre', methods: ['GET'])]
    public function softDeletelivre(Livres $livre, EntityManagerInterface $entityManager): RedirectResponse
    {
        $livre->setDeletedAt(new DateTime());

        $entityManager->persist($livre);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a bien été archivé !');
        return $this->redirectToRoute('show_livres');
    } // end function softDeleteLivre

    #[Route('/voir-les-archives', name: 'show_trash', methods: ['GET'])]
    public function showTrash(EntityManagerInterface $entityManager): Response
    {
        $livres = $entityManager->getRepository(Livres::class)->findAllArchived();

        return $this->render('admin/livre/show_trash.html.twig', [
            "livres" => $livres
        ]);
    } // end function showTrash

    #[Route('/restaurer-un-livre/{id}', name: 'restore_livre', methods: ['GET'])]
    public function restoreLivre(Livres $livre, LivresRepository $repository): RedirectResponse
    {
        $livre->setDeletedAt(null);

        $repository->add($livre, true);

        $this->addFlash('success', "Le livre a bien été restauré !");
        return $this->redirectToRoute('show_trash');
    } // end function restoreLivre()

    #[Route('/supprimer-un-livre/{id}', name: 'hard_delete_livre', methods: ['GET'])]
    public function hardDeleteLivre(Livres $livre, LivresRepository $repository): RedirectResponse
    {
        $repository->remove($livre, true);

        $this->addFlash('success', "Le livre a bien été supprimé definitivement du système !");
        return $this->redirectToRoute('show_trash');
    } // end function hardDeleteLivre()

    // Membres

    #[Route('/liste-des-membres', name: 'show_membres', methods: ['GET'])]
    public function showMembres(EntityManagerInterface $entityManager): Response
    {
        $membres = $entityManager->getRepository(User::class)->findby(['deletedAt' => null]);

        return $this->render('admin/membre/show_membres.html.twig', [
            'membres' => $membres
        ]);
    } // end function showMembres

    #[Route('/bannir-un-membre/{id}', name: 'soft_delete_membre', methods: ['GET'])]
    public function softDeleteMembre(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user->setDeletedAt(new DateTime());

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Le membre a bien été banni !');
        return $this->redirectToRoute('show_membres');
    } // end function softDeleteMembre

    #[Route('/voir-les-membres-bannis', name: 'show_ban', methods: ['GET'])]
    public function showBan(EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findAllArchived();

        return $this->render('admin/membre/show_ban.html.twig', [
            "membres" => $user
        ]);
    } // end function showBan

    #[Route('/restaurer-un-membre/{id}', name: 'restore_membre', methods: ['GET'])]
    public function restoreMembre(User $user, UserRepository $repository): RedirectResponse
    {
        $user->setDeletedAt(null);

        $repository->add($user, true);

        $this->addFlash('success', "Le membre a bien été restauré !");
        return $this->redirectToRoute('show_ban');
    } // end function restoreMembre()

    #[Route('/supprimer-un-membre/{id}', name: 'hard_delete_membre', methods: ['GET'])]
    public function hardDeleteMembre(User $user, UserRepository $repository): RedirectResponse
    {
        $repository->remove($user, true);

        $this->addFlash('success', "Le membre a bien été supprimé definitivement du système !");
        return $this->redirectToRoute('show_ban');
    } // end function hardDeleteMembre()
}
