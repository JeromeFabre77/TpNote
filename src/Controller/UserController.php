<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{


    #[Route('/register', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);


        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setPhoneNumber($data['phoneNumber']);

        $user->setRoles($data['roles'] ?? ["ROLE_USER"]);

        $em->persist($user);
        $em->flush();

        return $this->json(
            ['message' => 'User created successfully'],
            Response::HTTP_CREATED
        );
    }


    #[Route('/user/{id}', name: 'get_one_user', methods: ['GET'])]
    public function getUserById($id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        if ($user) {
            $data = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'phone number' => $user->getPhoneNumber(),
                'roles' => $user->getRoles(),
                'Réservation' => $user->getReservations()->map(fn(Reservation $reservation) => $reservation->getEventName())->getValues()
            ];
            return $this->json($data, Response::HTTP_OK);
        }
        return $this->json(['message' => "the user with id : $id not found"], Response::HTTP_NOT_FOUND);
    }

    #[Route('/users/all', name: 'get_all_user', methods: ['GET'])]
    public function getAllUsers(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'phone number' => $user->getPhoneNumber(),
                'roles' => $user->getRoles(),
                'Réservation' => $user->getReservations()->map(fn(Reservation $reservation) => $reservation->getEventName())->getValues()
            ];
        }
        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/user/modify/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return new JsonResponse(
                ['message' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        $data = json_decode($request->getContent(), true);
        $user->setName($data['name']);
        $user->setPhoneNumber($data['phoneNumber']);
        $em->flush();
        return $this->json(['message' => "the user with id : $id updated sucessfull"], Response::HTTP_OK);
    }

    #[Route('/user/delete/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            return $this->json(
                ['message' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        $emailUser = $user->getEmail();
        $em->remove($user);
        $em->flush();
        return $this->json(
            ['message' => "User deleted with email : $emailUser"],
            Response::HTTP_OK
        );
    }


    #[Route('/user/reservation', name: 'add_user_to_reservation', methods: ['POST'])]
    public function addUserToReservation(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        $reservation = $em->getRepository(Reservation::class)->findOneBy(['eventName' => $data['eventName']]);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], 404);
        }

        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found.'], 404);
        }

        $now = new \DateTime();
        if ($reservation->getDate() < $now->modify('+24 hours')) {
            return $this->json(['error' => 'Reservations must be made at least 24 hours in advance.'], Response::HTTP_NOT_ACCEPTABLE);
        }

        $reservation->setRelations($user);
        $em->persist($reservation);
        $em->flush();

        return $this->json(['success' => 'User added to reservation successfully.'], Response::HTTP_OK);
    }


    #[Route('/user/reservation/{email}', name: 'get_reservations_by_email', methods: ['GET'])]
    public function getReservationsByEmail(string $email, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['error' => 'User not found.'], 404);
        }

        $reservations = $user->getReservations();

        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'eventName' => $reservation->getEventName(),
                'date' => $reservation->getDate()->format('Y-m-d H:i:s'),
                'timeSlot' => $reservation->getTimeSlot()->format('%h hours %i minutes'),
            ];
        }

        return $this->json(['reservations' => $data], Response::HTTP_OK);
    }
}

