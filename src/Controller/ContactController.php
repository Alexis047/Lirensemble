<?php

namespace App\Controller;

use \DateTime;
use App\Entity\Contact;
use App\Form\ContactFormType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            ->from(new Address($contact->getEmail(), $contact->getPrenom()))
            ->to(new Address('alexis@lirensemble.com', 'Lirensemble'))
            ->subject($contact->getSujet())
            ->text($contact->getMessage())
            ->html('<p>' . $contact->getMessage() . '</p>');

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé !');
        } //end if

        return $this->render('contact/form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
