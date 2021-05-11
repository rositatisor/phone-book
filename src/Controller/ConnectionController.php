<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Connection;
use App\Entity\Contact;
use App\Entity\User;

class ConnectionController extends AbstractController
{
    /**
     * @Route("/connection", name="connection_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('connection/index.html.twig', [
            'controller_name' => 'ConnectionController',
        ]);
    }

    /**
     * @Route("/share/{id}", name="connection_share", methods={"POST"})
     */
    public function share(Request $r): Response
    {
        $user = $this->getUser();

        $contact = $this->getDoctrine()
            ->getRepository(Contact::class)
            ->find($r->get('id'));

        $guest = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['email' => $r->request->get('guest_email')]);

        $connection = new Connection;
        $connection
            ->setUser($user)
            ->setContact($contact)
            ->setGuest($guest[0]);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($connection);
        $entityManager->flush();

        return $this->render('connection/index.html.twig', [
            'controller_name' => 'ConnectionController',
        ]);
    }

}
