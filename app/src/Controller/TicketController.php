<?php

// src/Controller/TicketController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;

use App\Form\TicketType;

use App\Entity\Ticket;
#[IsGranted('ROLE_USER')]
#[Route('/tickets')]
class TicketController extends AbstractController
{

     
    #[Route('/', name: "tickets_list")]
    public function list(EntityManagerInterface $em): Response
    {
        $tickets = $em->getRepository(Ticket::class)->findAll();
        // dd($tickets);

        return $this->render('tickets/list.html.twig', [
            'tickets' => $tickets,
        ]);
    }
    #[Route('/new', name: "tickets_new")]
    public function new (Request $request, EntityManagerInterface $em): Response
    {
        $ticket = new Ticket();

        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $ticket = $form->getData();
            $ticket->setUser($this->getUser());

            $em->persist($ticket);
            $em->flush();

            $this->addFlash(
                'success',
                'Ticket bien créé !'
            );
            
            
            return $this->redirectToRoute('tickets_list');
        }
    
        return $this->renderForm('tickets/new.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/{ticketId}/edit', name: "tickets_edit")]
  
    public function edit ( Request $request, EntityManagerInterface $em, int $ticketId ): Response
    {
        
    $ticket = $em->getRepository(Ticket::class)->find($ticketId);
        $form = $this->createForm(TicketType::class, $ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            if ($ticket->getUser() == $this->getUser()) {
                $ticket = $form->getData();
                $em->persist($ticket);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Ticket bien mise à jour !'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Vous n\'avez pas les droits pour cette action !'
                );
            }
            
            return $this->redirectToRoute('tickets_list');
        }
    
        return $this->renderForm('tickets/edit.html.twig', [
            'form' => $form,
            'ticket' => $ticket
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