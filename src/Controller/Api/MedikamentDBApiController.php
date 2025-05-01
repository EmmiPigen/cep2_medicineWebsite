<?php
namespace App\Controller\Api;

use App\Entity\User;
use App\Entity\Udstyr;
use App\Enum\Lokale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedikamentDBApiController extends AbstractController
{
    #[Route('/api/{event}/{userId}', name: 'api_post', methods: ['POST'])]
    public function apiPost(
        Request $request,
        EntityManagerInterface $entityManager,
        string $event,
        int $userId,
    ): Response {
        // Handle the POST request
        if ($event == 'sendUdstyrListeInfo') {
            $data = json_decode($request->getContent(), true);

            // Check if the userId is valid
            $user = $entityManager->getRepository(User::class)->find($userId);
            if (! $user) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if the data is valid
            if (! isset($data['udstyrData']) || ! is_array($data['udstyrData'])) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'Invalid data format',
                    'data'    => $data,
                ], Response::HTTP_BAD_REQUEST);
            }
            // Loop through the udstyrData and create Udstyr entities

            foreach ($data['udstyrData'] as $udstyrListe) {
                // Create a new Udstyr entity and set its properties
                // Check if it already exists
                $existingUdstyr = $entityManager->getRepository(Udstyr::class)->findOneBy([
                    'userId' => $user,
                    'enhed'  => $udstyrListe['type'],
                ]);

                if ($existingUdstyr) {
                    //Update the existing Udstyr entity if needed
                    $existingUdstyr->setStatus($udstyrListe['status']);
                    $existingUdstyr->setPower($udstyrListe['power']);
                    $existingUdstyr->setLokale(Lokale::from($udstyrListe['lokale'])); // Assuming 'lokale' is a valid enum value
                    continue; // Skip if the Udstyr already exists
                }
                $udstyr = new Udstyr();
                $udstyr->setUserId($user);
                $udstyr->setEnhed($udstyrListe['type']);
                $udstyr->setStatus($udstyrListe['status']);
                $udstyr->setPower($udstyrListe['power']);
                $udstyr->setLokale(Lokale::from($udstyrListe['lokale'])); // Assuming 'lokale' is a valid enum value

                // Persist the Udstyr entity to the database
                $entityManager->persist($udstyr);
                $entityManager->flush();
            }

            return new JsonResponse([
                'status'  => 'success',
                'message' => 'Data received successfully',
            ], Response::HTTP_OK);

        }

        // If the event is not recognized, return an error response
        return new JsonResponse([
            'status'  => 'error',
            'message' => 'Invalid event or userId',
        ], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/api/{event}/{userId}', name: 'api_get', methods: ['GET'])]
    public function apiGet(
        Request $request,
        EntityManagerInterface $entityManager,
        string $event,
        int $userId,
    ): Response {
        // Handle the GET request
        if ($event == 'getUserMedikamentListe') {
            //Check if the userId is valid
            $user = $entityManager->getRepository(User::class)->find($userId);
            if (! $user) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            //Fetch the user's medicament list
            $medikamentListe = $entityManager->getRepository(User::class)->find($userId)->getMedikamentListes();

            //Check if the user has any medicament list
            if ($medikamentListe->isEmpty()) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'No medicament list found for this user',
                ], Response::HTTP_NOT_FOUND);
            }

            //Send only the data we need to the client
            $medikamentData = [];
            // Loop through the medicament list and extract the data we need
            foreach ($medikamentListe as $med) {
                $medikamentData[] = [
                    'name'         => $med->getMedikamentNavn(),
                    'timeInterval' => $med->getTimeInterval(),
                    'timesToTake'  => $med->getTidspunkterTages(),
                    'dose'         => $med->getDosis(),
                    'unit'         => $med->getEnhed(),
                    'priority'     => $med->getPrioritet()
                ];
            }
            // Return the data as a JSON response
            return new JsonResponse([
                'status'  => 'success',
                'message' => 'Data received successfully',
                'list'    => $medikamentData,
            ], Response::HTTP_OK);

        }

        // If the event is not recognized, return an error response
        return new JsonResponse([
            'status'  => 'error',
            'message' => 'Invalid event or userId',
        ], Response::HTTP_BAD_REQUEST);

    }
}
