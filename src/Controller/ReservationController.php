<?php

namespace App\Controller;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'create_reservation', methods: ['POST'])]
    public function createReservation(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $reservation = new Reservation();
        $reservation->setEventName($data['name']);
        $date = new \DateTime($data['date']);
        $reservation->setDate($date);
        $timeSlot = new \DateInterval($data['timeSlot']);
        $reservation->setTimeSlot($timeSlot);


        $em->persist($reservation);
        $em->flush();

        return $this->json(
            ['message' => 'Reservation created successfully'],
            Response::HTTP_CREATED
        );
    }

    #[Route('/reservation', name: 'create_reservation', methods: ['GET'])]
    public function getAllReservation(EntityManagerInterface $em): Response
    {
        $reservations = $em->getRepository(Reservation::class)->findAll();
        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'name' => $reservation->getEventName(),
                'email' => $reservation->getDate(),
                'phone number' => $reservation->getTimeSlot(),
            ];
        }
        return $this->json($data, Response::HTTP_OK);
    }
}
