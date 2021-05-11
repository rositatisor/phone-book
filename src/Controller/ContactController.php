<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;
use App\Entity\Connection;
use App\Entity\User;

class ContactController extends AbstractController
{
    private function getContactById(Request $r)
    {
        return $this->getDoctrine()
            ->getRepository(Contact::class)
            ->find($r->get('id'));
    }

    /**
     * @Route("/contact", name="contact_index", methods={"GET"})
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $contacts = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->findBy(['user' => $user->getId()]);

        $giving_access = $this->getDoctrine()
            ->getRepository(Connection::class)
            ->findBy(['user' => $user->getId()]);

        $receiving_access = $this->getDoctrine()
            ->getRepository(Connection::class)
            ->findBy(['guest' => $user->getId()]);

        $guests = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts,
            'giving_access' => $giving_access,
            'receiving_access' => $receiving_access,
            'guests' => $guests
        ]);
    }

    /**
     * @Route("/contact/create", name="contact_create", methods={"GET"})
     */
    public function create(Request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $contact_name = $r->getSession()->getFlashBag()->get('contact_name', []);
        $contact_phone = $r->getSession()->getFlashBag()->get('contact_phone', []);

        return $this->render('contact/create.html.twig', [
            'contact_name' => $contact_name[0] ?? '',
            'contact_phone' => $contact_phone[0] ?? ''
        ]);
    }

    /**
     * @Route("/contact/store", name="contact_store", methods={"POST"})
     */
    public function store(Request $r): Response
    {
        $user = $this->getUser();

        $contact = new Contact;
        $contact
            ->setName($r->request->get('contact_name'))
            ->setPhone($r->request->get('contact_phone'))
            ->setUser($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->redirectToRoute('contact_index');
    }

    /**
     * @Route("/contact/edit/{id}", name="contact_edit", methods={"GET"})
     */
    public function edit(Request $r): Response
    {
        $contact = $this->getContactById($r);

        $contact_name = $r->getSession()->getFlashBag()->get('contact_name', []);
        $contact_phone = $r->getSession()->getFlashBag()->get('contact_phone', []);

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'contact_name' => $contact_name[0] ?? '',
            'contact_phone' => $contact_phone[0] ?? ''
        ]);
    }

    /**
     * @Route("/contact/update/{id}", name="contact_update", methods={"POST"})
     */
    public function update(Request $r): Response
    {
        $user = $this->getUser();

        $contact = $this->getContactById($r);

        $contact
            ->setName($r->request->get('contact_name'))
            ->setPhone($r->request->get('contact_phone'))
            ->setUser($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->redirectToRoute('contact_index');
    }

    /**
     * @Route("/contact/delete/{id}", name="contact_delete", methods={"POST"})
     */
    public function delete(Request $r): Response
    {
        $contact = $this->getContactById($r);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($contact);
        $entityManager->flush();

        return $this->redirectToRoute('contact_index');
    }

    /**
     * @Route("/contact/share/{id}", name="contact_share", methods={"POST"})
     */
    public function share(Request $r): Response
    {
        $user = $this->getUser();

        $contact = $this->getContactById($r);

        $contact_id = $this->getDoctrine()
            ->getRepository(Connection::class)
            ->findBy([
                'user' => $user->getId(),
                'contact' => $contact->getId()
            ]);
        
        if ($contact_id) {
            $guest_email = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['id' => $contact_id[0]->getGuest()]);
        } else {
            $guest_email = $r->getSession()->getFlashBag()->get('guest_email', []);
        }

        return $this->render('contact/share.html.twig', [
            'contact' => $contact,
            'guest_email' => $guest_email[0] ?? ''
        ]);
    }
}
