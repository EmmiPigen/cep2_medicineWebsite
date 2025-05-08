<?php
namespace App\Controller\Api;

use App\Entity\MedikamentLog;
use App\Entity\Udstyr;
use App\Entity\User;
use App\Enum\Lokale;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedikamentDBApiController extends AbstractController
{
    //
    // POST data to DB using API
    //
    #[Route('/api/{event}/{userId}', name: 'api_post_event', methods: ['POST'])]
    public function apiPost(
        Request $request,
        EntityManagerInterface $entityManager,
        string $event,
        int $userId,
    ): Response {
        // post Udstyrliste to DB
        if ($event == 'sendUdstyrListeInfo') {
            // Get the base64 string from the request body
            $base64_string = $request->getContent();
            // Decode the base64 string to get the JSON data
            $data = base64_decode($base64_string);
            // Decode the JSON data into an associative array
            $data = json_decode($data, true);
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

            //Fetch the user's existing udstyr list
            $existingUdstyrList = $entityManager->getRepository(Udstyr::class)->findBy(['userId' => $user]);

            //Keep track of which "enhed" types are present in the incoming data
            $receivedEnhedTypes = [];

            // Loop through the udstyrData and create Udstyr entities
            foreach ($data['udstyrData'] as $udstyrListe) {
                $receivedEnhedTypes[] = $udstyrListe['enhed']; // Store the enhed

                $existingUdstyr = $entityManager->getRepository(Udstyr::class)->findOneBy([
                    'userId' => $user,
                    'enhed'  => $udstyrListe['enhed'],
                ]);

                if ($existingUdstyr) {
                    $existingUdstyr->setStatus($udstyrListe['status']);
                    $existingUdstyr->setPower($udstyrListe['power']);
                    $existingUdstyr->setLokale(Lokale::from($udstyrListe['lokale'])); // Assuming 'lokale' is a valid enum value
                    $entityManager->persist($existingUdstyr);
                } else {
                    $udstyr = new Udstyr();
                    $udstyr->setUserId($user);
                    $udstyr->setEnhed($udstyrListe['enhed']);
                    $udstyr->setStatus($udstyrListe['status']);
                    $udstyr->setPower($udstyrListe['power']);
                    $udstyr->setLokale(Lokale::from($udstyrListe['lokale'])); // Assuming 'lokale' is a valid enum value
                    $entityManager->persist($udstyr);
                }
            }

            // Set the status of existing Udstyr entities to "offline" if they are not present in the incoming data

            foreach ($existingUdstyrList as $udstyr) {
                if (! in_array($udstyr->getEnhed(), $receivedEnhedTypes)) {
                    $udstyr->setStatus('offline');    // Set the status to "offline"
                    $entityManager->persist($udstyr); // Persist the changes
                }
            }

            // Flush the changes to the database
            $entityManager->flush();

            // Return a success response
            return new JsonResponse([
                'status'  => 'success',
                'message' => 'Data received successfully',
            ], Response::HTTP_OK);

        }

        if ($event === 'MedicationRegistrationLog') {
            // Decode the base64 string to get the JSON data
            $base64_string = $request->getContent();
            $data          = base64_decode($base64_string);
            // Decode the JSON data into an associative array
            $data = json_decode($data, true);

            // Check if the userId is valid
            $user = $entityManager->getRepository(User::class)->find($userId);
            if (! $user) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            // Check if the data is valid
            if (! isset($data['medicationLog']) || ! is_array($data['medicationLog'])) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'Invalid data format',
                    'data'    => $data,
                    'event'   => $event,
                ], Response::HTTP_BAD_REQUEST);
            }

            //Create the new medication registration log entry
            $medication = new MedikamentLog();
            $medication->setUserId($user);
            $medication->setMedikamentNavn($data['medicationLog']['name']);
            $medication->setTagetTid(new \DateTime($data['medicationLog']['tagetTid']));
            $medication->setTagetStatus($data['medicationLog']['status']);
            $medication->setTagetLokale(Lokale::from($data['medicationLog']['lokale'])); // Assuming 'lokale' is a valid enum value

            $entityManager->persist($medication);
            $entityManager->flush();

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
    //
    // GET data from DB using API
    //
    #[Route('/api/{event}/{userId}', name: 'api_get_event', methods: ['GET'])]
    public function apiGet(
        Request $request,
        EntityManagerInterface $entityManager,
        string $event,
        int $userId,
    ): Response {
        // Handle the GET request
        if ($event === 'getUserMedikamentListe') {
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
                    'priority'     => $med->getPrioritet(),
                ];
            }

            //Base64 encode the data to send it as a JSON response
            $encodedData = base64_encode(json_encode($medikamentData));

            // Return the data as a JSON response
            return new JsonResponse([
                'status'  => 'success',
                'message' => 'Request recieved successfully',
                'list'    => $encodedData,
            ], Response::HTTP_OK);

        }

        # Get User id
        if ($event == 'getUserId') {
            $user = $entityManager->getRepository(User::class)->find($userId);

            if (! $user) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'User not found',
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'status' => 'success',
                'userId' => $user->getUserId(),
                // Optional: include more user data if needed
            ], Response::HTTP_OK);
        }
        // If the event is not recognized, return an error response
        return new JsonResponse([
            'status'  => 'error',
            'message' => 'Invalid event or userId',
        ], Response::HTTP_BAD_REQUEST);

    }
}
