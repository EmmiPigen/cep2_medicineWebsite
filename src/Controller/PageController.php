<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\MedikamentListeRepository;
use App\Entity\MedikamentListe;

use App\Entity\MedikamentLog;
use App\Repository\MedikamentLogRepository;

use App\Entity\Udstyr;
use App\Repository\UdstyrRepository;

use Psr\Log\LoggerInterface;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RegistrationFormType;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Security;



use App\Repository\MedikamentRepository;

class PageController extends AbstractController
{
  #[Route('/', name: 'home')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function show(
    MedikamentLogRepository $medikamentLog, 
    MedikamentListeRepository $medikamentListe,
    LoggerInterface $logger
  ): Response {
    //Get the current logged in user
    $user = $this->getUser();

    if (!$user){
      throw $this->createAccessDeniedException('Du skal være logget ind for at se denne side.');
    }

    //Get user id
    $medLogs = $user->getMedikamentLogs(); // Get the user's medication logs
    $medList = $user->getMedikamentListes(); // Get the user's medication list
    
    if ($medLogs->isEmpty()) {
      $logger->warning('Der er ingen medicin logget for denne bruger!');
      // Handle the case when there are no logs for the user
      $lastLog = [
        'medikamentNavn' => 'Ingen medicin logget',
        'tagetTid' => 'Ingen tid logget',
        'tagetStatus' => TRUE,
        'tagetLokale' => 'Ingen lokation logget',
      ];

    } else {
      $lastLog = $logs->last(); // Get the last log entry
    }

    if ($medList->isEmpty()) {
      $logger->warning('Du har ingen medicin du skal tage!');
      // Handle the case when there are no medications in the list
      $nextMeds = [
        'medikamentNavn' => 'Ingen medicin tilgængelig',
        'tidspunktTages' => 'Intet tidspunt tilgængelig',
        'tidsinterval' => 'Ingen tidsinterval tilgængelig',
      ];
    } else {
      $nextMeds = 
    }
    /* $medikamentList = $medikament->findAll();

    if (empty($medikamentList)) {
      $logger->warning('Der er ingen medicin i databasen!');


      //default data in when no medikament in database
      $medikamentData = [
        'medickament' => 'Ingen medicin tilgængelig',
        'dosis' => 'Ingen dosis tilgængelig',
        'unit' => 'Ingen enhed tilgængelig',
        'priority' => 'Ingen prioritet tilgængelig',
        'timeInterval' => 'Ingen tidsinterval tilgængelig',
        'amount' => 'Ingen mængde tilgængelig',
        'timeTaken' => 'Ingen tid taget tilgængelig',
        'medicineStatus' => 1
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
    */
    //Render the template with the medication data
    return $this->render('page/home.html.twig',[
      'lastLog' => $lastLog]);
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

  #[Route(path: '/login', name: 'login')]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
      // get the login error if there is one
      $error = $authenticationUtils->getLastAuthenticationError();

      // last username entered by the user
      $lastUsername = $authenticationUtils->getLastUsername();

      return $this->render('page/login.html.twig', [
          'last_username' => $lastUsername,
          'error' => $error,
      ]);
  }

  #[Route('/logout', name: 'logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  #[Route('/profil', name: 'profil')]
  public function profil(): Response
  {
    return $this->render('page/profil.html.twig', []);
  }

  #[Route('/register', name: 'register')]
  public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
  {
      $user = new User();
      $form = $this->createForm(RegistrationFormType::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          /** @var string $plainPassword */
          $plainPassword = $form->get('plainPassword')->getData();

          // encode the plain password
          $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

          $entityManager->persist($user);
          $entityManager->flush();

          // do anything else you need here, like send an email

          return $this->redirectToRoute('home');
      }

      return $this->render('utility/register.html.twig', [
          'registrationForm' => $form,
      ]);
  }

}


