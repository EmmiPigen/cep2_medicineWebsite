<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UdstyrController extends AbstractController
{
    #[Route('/udstyr', name: 'app_udstyr')]
    public function index(): Response
    {
        return $this->render('udstyr/index.html.twig', [
            'controller_name' => 'UdstyrController',
        ]);
    }
}
