<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;

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

        $contacts = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->findAll();

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts
        ]);
    }

    /**
     * @Route("/contact/create", name="contact_create", methods={"GET"})
     */
    public function create(Request $r): Response
    {
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
        $contact = new Contact;
        $contact
            ->setName($r->request->get('contact_name'))
            ->setPhone($r->request->get('contact_phone'));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        return $this->redirectToRoute('contact_index');
    }

    /**
     * @Route("/contact/edit/{id}", name="contact_edit", methods={"GET"})
     */
    public function edit(int $id, Request $r): Response
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
    public function update(Request $r, $id): Response
    {
        $contact = $this->getContactById($r);

        $contact
            ->setName($r->request->get('contact_name'))
            ->setPhone($r->request->get('contact_phone'));

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

}
