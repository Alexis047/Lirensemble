<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Livres;
use App\Entity\Emprunt;
use App\Form\ModifInfosFormType;
use App\Repository\EmpruntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/mon-profil', name: 'show_profile', methods: ['GET'])]
    public function showProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $mesLivres = $entityManager->getRepository(Livres::class)->findBy(array('proprietaire' => $user, 'deletedAt' => null));
        $monEmprunt = $entityManager->getRepository(Emprunt::class)->findBy(array('emprunteur' => $user, 'deletedAt' => null));

        return $this->render('profil/show_profile.html.twig', [
            "mesLivres" => $mesLivres,
            "monEmprunt" => $monEmprunt,
        ]);
    }

    #[Route('/modifier-mes-informations/{id}', name: 'update_infos', methods: ['GET', 'POST'])]
    public function updateInfos(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModifInfosFormType::class, $user)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user->setEmail($user->getEmail());
            $user->setPassword($user->getPassword());
            $user->setUpdatedAt(new DateTime());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'La modification est réussie avec succès !');
            return $this->redirectToRoute('show_profile');
        } // end if $form

        return $this->render('profil/form.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    } // end function update()

    #[Route('/annuler-un-emprunt/{id}', name: 'cancel_emprunt', methods: ['GET'])]
    public function cancelEmprunt(Emprunt $emprunt, EmpruntRepository $repository): RedirectResponse
    {
        $repository->remove($emprunt, true);

        $this->addFlash('success', "L'emprunt à bien été annulé' !");
        return $this->redirectToRoute('show_profile');
    } // end function cancelEmprunt()
}
