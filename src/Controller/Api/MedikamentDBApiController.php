<?php
namespace App\Controller\Api;

use App\Repository\MedikamentListe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MedikamentDBApiController extends AbstractController
{
    #[Route('/api', name: 'api')]
    public function api(
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        // Handle the POST request
        if ($request->isMethod('POST')) {
            // Retrieve the data from the request
            $data = $request->getContent();

            //On success return 200
            return new Response('success', 200);
        } else {
            //On failure return 400
            return new Response('error', 400);

        }
    }

}
