<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'user_register', methods: ['GET', 'POST'])]
    public function register(MailerInterface $mailer, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        if($this->getUser()) {
            return $this->redirectToRoute('default_home');
        }

        $user = new User();

        $users = $entityManager->getRepository(User::class);

        $form = $this->createForm(RegisterFormType::class, $user)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach( $users as $userdb) {
                if($userdb->getPseudo() === $user->getPseudo() || $userdb->getEmail() === $user->getEmail()) {
                    $this->addFlash('error', 'Ce pseudo ou cet email appartient déjà à un lecteur');
                    return $this->redirectToRoute('app_register');
                }

            }
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

            $email = (new TemplatedEmail())
                ->from(new Address('alexis@lirensemble.com', 'Lirensemble'))
                ->to(new Address($user->getEmail(), $user->getPseudo()))
                ->subject('Bienvenue sur Lirensemble')
                ->htmlTemplate('email/welcome.html.twig')
                ->context([
                    // 'user' => $user
                ]);

            $mailer->send($email);

            $this->addFlash('success', 'Votre inscription a été effectué avec succès !');
            return $this->redirectToRoute('app_login');
        } //end if

        return $this->render('register/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

