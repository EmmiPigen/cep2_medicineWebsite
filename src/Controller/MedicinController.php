<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MedicinController extends AbstractController
{
    #[Route('/medicin', name: 'app_medicin')]
    public function index(): Response
    {
        return $this->render('medicin/index.html.twig', [
            'controller_name' => 'MedicinController',
        ]);
    }
}
