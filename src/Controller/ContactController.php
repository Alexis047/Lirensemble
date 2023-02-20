<?php

namespace App\Controller;

use \DateTime;
use App\Entity\Contact;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $contact = new Contact();

        if($this->getUser()) {
            $contact->setPrenom($this->getUser()->getPrenom());
            $contact->setNom($this->getUser()->getNom());
            $contact->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactFormType::class, $contact)
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new Datetime());
            
            $entityManager->persist($contact);
            $entityManager->flush();

            //Email
            $email = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé !');
        } //end if

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
