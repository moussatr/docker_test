<?php

// src/Controller/TicketController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\TicketType;

use App\Entity\Ticket;

#[Route('/tickets')]
class TicketController extends AbstractController
{

    #[Route('/', name: "tickets_list")]
    public function list(ManagerRegistry $doctrine): Response
    {
        $tickets = $doctrine->getRepository(Ticket::class)->findAll();
        // dd($tickets);

        return $this->render('tickets/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }
    #[Route('/new', name: "tickets_new")]
    public function new(ManagerRegistry $doctrine, Request $request): Response{
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
  
            // but, the original `$task` variable has also been updated
            $ticket = $form->getData();

            $entityManager = $doctrine->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('tickets_list');
        }

        return $this->renderForm('tickets/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{ticketId}', name: "tickets_delete")]
    public function delete(ManagerRegistry $doctrine, int $ticketId):Response
    {
        $ticket = $doctrine->getRepository(Ticket::class)->find($ticketId);
        // dd($tickets);
        $entityManager = $doctrine->getManager();
        $entityManager->remove($ticket);
        $entityManager->flush();
        return $this->redirectToRoute('tickets_list');
    }
    #[Route('/edit/{ticketId}', name: "tickets_edit")]
    public function edit(ManagerRegistry $doctrine, int $ticketId):Response
    {
        $ticket = $doctrine->getRepository(Ticket::class)->find($ticketId);

        $entityManager = $doctrine->getManager();
       

        if (!$ticket) {
            throw $this->createNotFoundException(
                'No product found for id '.$ticketId
            );
        }

        $ticket->getLabel('edit');
        
        
        $entityManager->flush();

        return $this->redirectToRoute('tickets_list');
    }

    #[Route('/{ticketId}', name: "tickets_show")]
    public function show(?int $ticketId, ManagerRegistry $doctrine): Response
    {
        $ticket = $doctrine->getRepository(Ticket::class)->find($ticketId);

        if (!$ticket) {
            throw $this->createNotFoundException(
                'No ticket found for id ' . $ticketId
            );
        }

        return $this->render('tickets/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
}