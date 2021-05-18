<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Contact;

class FavouriteController extends AbstractController
{
    /**
     * @Route("/favourites", name="favourites_index", methods={"GET"})
     */
    public function index(Request $r): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $contacts = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->findBy(['user' => $user->getId()]);

        $favourites = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->findBy([
                'user' => $user->getId(),
                'favourite' => 'yes'
            ]);

        return $this->render('favourite/index.html.twig', [
            'favourites' => $favourites,
            'contacts' => $contacts,
            'success' => $r->getSession()->getFlashBag()->get('success', [])
        ]);
    }

    /**
     * @Route("/favourites/add", name="favourites_add", methods={"GET"})
     */
    public function add(Request $r): Response
    {
        $contact = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->find($r->query->get('contact_id'));

        $contact->setFavourite('yes');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', $contact->getName().' was added to Favourites.');

        return $this->redirectToRoute('favourites_index');
    }

    /**
     * @Route("/favourites/remove/{id}", name="favourites_remove", methods={"POST"})
     */
    public function remove(Request $r): Response
    {
        $contact = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->find($r->get('id'));

        $contact->setFavourite('no');

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        $r->getSession()->getFlashBag()->add('success', $contact->getName().' was deleted from Favourites.');
        
        return $this->redirectToRoute('favourites_index');
    }
}
