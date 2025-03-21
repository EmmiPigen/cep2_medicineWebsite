<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HistorikController extends AbstractController
{
    #[Route('/historik', name: 'app_historik')]
    public function index(): Response
    {
        return $this->render('historik/index.html.twig', [
            'controller_name' => 'HistorikController',
        ]);
    }
}
