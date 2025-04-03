<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Medikament;
use App\Repository\MedikamentRepository;
use Psr\Log\LoggerInterface;
use Twig\Extensions\IntlExtension;


class PageController extends AbstractController
{
  #[Route('/', name: 'home')]
  public function show(MedikamentRepository $medikamentRepository, LoggerInterface $logger): Response
  {
    $medikamentList = $medikamentRepository->findAll();

    if (empty($medikamentList)) {
      $logger->warning('Der er ingen medicin i databasen!');


      //default data in when no medikament in database
      $medikamentData = [
        'medikament' => 'Ingen medicin tilgængelig',
        'dosis' => 'Ingen dosis tilgængelig',
        'unit' => 'Ingen enhed tilgængelig',
        'priority' => 'Ingen prioritet tilgængelig',
        'timeInterval' => 'Ingen tidsinterval tilgængelig',
        'amount' => 'Ingen mængde tilgængelig',
        'timeTaken' => 'Ingen tid taget tilgængelig',
        'medicineStatus' => true
      ];
    } else {
      $medikament = $medikamentList[array_rand($medikamentList)];

      if (!$medikament instanceof Medikament) {
        throw new \Exception('Invalid medikament object');
      }

      // Prepare medication data for rendering
      $medikamentData = [
        'medikament' => $medikament->getName(),
        'dosis' => $medikament->getDosis(),
        'unit' => $medikament->getUnit(),
        'priority' => $medikament->getPriority(),
        'timeInterval' => $medikament->getTimeInterval(),
        'amount' => $medikament->getAmount(),
        'timeTaken' => $medikament->getTimeTaken()->format('H:i'),
        'medicineStatus' => $medikament->isTakenStatus()
      ];
    }

    //Render the template with the medication data
    return $this->render('page/home.html.twig', $medikamentData);
  }

  #[Route('/medicin', name: 'medicin')]
  public function historik(): Response
  {
    return $this->render('page/medicin.html.twig', []);
  }

  #[Route('/historik', name: 'historik')]
  public function showMedicin(MedikamentRepository $medikamentRepository): Response
  {
    // Fetch all medications from the repository
    $medicinList = $medikamentRepository->findAll();

    // Check if the list is empty and handle accordingly
    if (empty($medicinList)) {
      return $this->render('page/medicin.html.twig', [
        'medicinList' => [],
        'message' => 'Ingen medicin tilgængelig'
      ]);
    }

    // Render the template with the medication list
    return $this->render('page/historik.html.twig', [
      'medicinList' => $medicinList,
    ]);
  }

  #[Route('/udstyr', name: 'udstyr')]
  public function udstyr(): Response
  {
    return $this->render('page/udstyr.html.twig', []);
  }

  #[Route('/hjaelp', name: 'hjaelp')]
  public function hjaelp(): Response
  {
    return $this->render('page/hjaelp.html.twig', []);
  }

  #[Route('/login', name: 'login')]
  public function login(): Response
  {
    return $this->render('page/login.html.twig', []);
  }

  #[Route('/profil', name: 'profil')]
  public function profil(): Response
  {
    return $this->render('page/profil.html.twig', []);
  }
}
