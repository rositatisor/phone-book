<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    private function successMessage(Request $r, Contact $contact, string $type)
    {
        switch ($type) {
            case 'create':
                $r->getSession()->getFlashBag()->add('success', 'Contact '.$contact->getName().' with phone '.$contact->getPhone().' was created.');
                break;
            case 'edit':
                $r->getSession()->getFlashBag()->add('success', 'Contact '.$contact->getName().' with phone '.$contact->getPhone().' was updated.');
                break;
            case 'delete':
                $r->getSession()->getFlashBag()->add('success', 'Contact '.$contact->getName().' was deleted.');
                break;
        }
    }

    /**
     * @Route("/contact", name="contact_index", methods={"GET"})
     */
    public function index(Request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $contacts = $this->getDoctrine()
            ->getRepository(Contact::class);
            if ($r->query->get('sort') == 'name_az') $contacts = $contacts->findBy(['user' => $user->getId()], ['name' => 'asc']);
            elseif ($r->query->get('sort') == 'name_za') $contacts = $contacts->findBy(['user' => $user->getId()], ['name' => 'desc']);
            else $contacts = $contacts->findBy(['user' => $user->getId()]);

        $guests = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('contact/index.html.twig', [
            'contacts' => $contacts,
            'guests' => $guests,
            'sortBy' => $r->query->get('sort') ?? 'default',
            'success' => $r->getSession()->getFlashBag()->get('success', []),
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/giving", name="contact_index_giving", methods={"GET"})
     */
    public function giving(Request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $giving_access = $this->getDoctrine()
            ->getRepository(Connection::class)
            ->findBy(['user' => $user->getId()]);

        $guests = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('contact/giving.html.twig', [
            'giving_access' => $giving_access,
            'guests' => $guests,
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/receiving", name="contact_index_receiving", methods={"GET"})
     */
    public function receiving(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $receiving_access = $this->getDoctrine()
            ->getRepository(Connection::class)
            ->findBy(['guest' => $user->getId()]);

        $guests = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('contact/receiving.html.twig', [
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
            'contact_phone' => $contact_phone[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/contact/store", name="contact_store", methods={"POST"})
     */
    public function store(Request $r, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();

        $contact = new Contact;
        $contact
            ->setName($r->request->get('contact_name'))
            ->setPhone($r->request->get('contact_phone'))
            ->setUser($user);

        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            $r->getSession()->getFlashBag()->add('contact_name', $r->request->get('contact_name'));
            $r->getSession()->getFlashBag()->add('contact_phone', $r->request->get('contact_phone'));

            return $this->redirectToRoute('contact_create');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $this->successMessage($r, $contact, 'create');

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
            'contact_phone' => $contact_phone[0] ?? '',
            'errors' => $r->getSession()->getFlashBag()->get('errors', [])
        ]);
    }

    /**
     * @Route("/contact/update/{id}", name="contact_update", methods={"POST"})
     */
    public function update(Request $r, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();

        $contact = $this->getContactById($r);

        $contact
            ->setName($r->request->get('contact_name'))
            ->setPhone($r->request->get('contact_phone'))
            ->setUser($user);
        
        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $r->getSession()->getFlashBag()->add('errors', $error->getMessage());
            }
            return $this->redirectToRoute('contact_edit', ['id'=>$contact->getId()]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $this->successMessage($r, $contact, 'edit');

        return $this->redirectToRoute('contact_index');
    }

    /**
     * @Route("/contact/delete/{id}", name="contact_delete", methods={"POST"})
     */
    public function delete(Request $r): Response
    {
        $contact = $this->getContactById($r);

        if ($contact->getConnections()->count() > 0) {
            $r->getSession()->getFlashBag()->add('errors', 'Selected contact '.$contact->getName().' cannot be deleted, hence has connections assigned.');
            return $this->redirectToRoute('contact_index');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($contact);
        $entityManager->flush();

        $this->successMessage($r, $contact, 'delete');
        
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
