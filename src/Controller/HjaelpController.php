<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HjaelpController extends AbstractController
{
    #[Route('/hjaelp', name: 'app_hjaelp')]
    public function index(): Response
    {
        return $this->render('hjaelp/index.html.twig', [
            'controller_name' => 'HjaelpController',
        ]);
    }
}
