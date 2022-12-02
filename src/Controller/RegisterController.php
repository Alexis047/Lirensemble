<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'user_register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if($this->getUser()) {
            return $this->redirectToRoute('default_home');
        }

        $user = new User();

        $form = $this->createForm(RegisterFormType::class, $user)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new Datetime());
            $user->setUpdatedAt(new Datetime());
            $user->setRoles(['ROLE_USER']);

            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user, $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre inscription a été effectué avec succès !');
            return $this->redirectToRoute('default_home');
        } //end if

        return $this->render('register/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

