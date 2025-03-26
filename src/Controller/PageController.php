<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Medikament;
use App\Repository\MedikamentRepository;


class PageController extends AbstractController
{
  #[Route('/', name: 'home')]
  public function show(MedikamentRepository $medikamentRepository): Response
  {

    $medikament = $medikamentRepository->find(rand(30, 39));
    
    return $this->render('page/home.html.twig', [
      'controller_name' => 'HomeController',
      'medikament' => $medikament->getName(),
      'dosis' => $medikament->getDosis(),
      'unit' => $medikament->getUnit(),
      'priority' => $medikament->getPriority(),
      'timeInterval' => $medikament->getTimeInterval(),
      'amount' => $medikament->getAmount(),
      'timeTaken' => $medikament->getTimeTaken()->format('H:i'),
      'medicineStatus' => $medikament->isTakenStatus()
    ]);
  }

  #[Route('/medicin', name: 'medicin')]
  public function medicin(): Response
  {
    return $this->render('page/medicin.html.twig', [
      'controller_name' => 'MedicinController',
    ]);
  }

  #[Route('/historik', name: 'historik')]
  public function historik(): Response
  {
    return $this->render('page/historik.html.twig', [
      'controller_name' => 'HistorikController',
    ]);
  }

  #[Route('/udstyr', name: 'udstyr')]
  public function udstyr(): Response
  {
    return $this->render('page/udstyr.html.twig', [
      'controller_name' => 'UdstyrController',
    ]);
  }

  #[Route('/hjaelp', name: 'hjaelp')]
  public function hjaelp(): Response
  {
    return $this->render('page/hjaelp.html.twig', [
      'controller_name' => 'HjaelpController',
    ]);
  }

  #[Route('/login', name: 'login')]
  public function login(): Response
  {
    return $this->render('page/login.html.twig', [
      'controller_name' => 'LoginController',
    ]);
  }

  #[Route('/profil', name: 'profil')]
  public function profil(): Response
  {
    return $this->render('page/profil.html.twig', [
      'controller_name' => 'ProfilController',
    ]);
  }
}
