<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\MedikamentListe;
use App\Form\RegistrationFormType;
use App\Form\AddMedicinType;
use App\Repository\MedikamentListeRepository;
use App\Repository\MedikamentLogRepository;
use App\Repository\UdstyrRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

date_default_timezone_set('Europe/Copenhagen');
setlocale(LC_TIME, 'da_DK');
class PageController extends AbstractController
{
  #[Route('/', name: 'home')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function home(
    MedikamentLogRepository $medikamentLog,
    MedikamentListeRepository $medikamentListe,
    LoggerInterface $logger
  ): Response {
    //Get the current logged in user
    $user = $this->getUser();
    $userName = $user->getFuldeNavn();

    if (!$user) {
      throw $this->createAccessDeniedException('Du skal være logget ind for at se denne side.');
    }

    //Check if the user has any medications for today
    $now = new \DateTime('');
    $nowTime = $now->format('H:i');

    $nextMedications = [];
    foreach ($user->getMedikamentListes() as $med) {
      $times = $med->getTidspunkterTages();
      //Filter times that are still ahead today

      $upcomingTimes = array_filter($times, function ($time) use ($nowTime) {
        return $time >= $nowTime;
      });

      // get the soonest time
      if (!empty($upcomingTimes)) {
        $nextTime = min($upcomingTimes);
        $tidsinterval = $med->getTimeInterval();

        $sidstTages = date("H:i", strtotime("+$tidsinterval minutes", strtotime($nextTime)));

        $nextMedications[] = [
          'medikamentNavn' => $med->getMedikamentNavn(),
          'tidspunktTages' => $nextTime,
          'sidstTages' => $sidstTages,
          'prioritet' => $med->getPrioritet(),
        ];
      }
    }

    //Sort the soonest upcoming times
    usort($nextMedications, fn($a, $b) => strcmp($a['tidspunktTages'], $b['tidspunktTages']));

    //Get all the remaining medications for today not taken yet
    $restMedsIdag = array_filter($nextMedications, function ($med) use ($nowTime) {
      return $med['tidspunktTages'] >= $nowTime;
    });


    $medLogs = $user->getMedikamentLogs(); // Get the user's medication logs
    if ($medLogs->isEmpty()) {
      $logger->warning('Der er ingen medicin logget for denne bruger!');
      // Handle the case when there are no logs for the user
      $lastLog = null;
    } else {
      $lastLog = $medLogs->last(); // Get the last log entry
    }

    //Render the template with the medication data
    return $this->render('page/home.html.twig', [
      'Navn' => $userName,
      'lastLog' => $lastLog,
      'restMedsIdag' => $restMedsIdag
    ]);
  }

  #[Route('/medicin', name: 'medicin')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function medicin(
    EntityManagerInterface $entityManager,
    AuthenticationUtils $authenticationUtils,
    Request $request,
  ): Response {
    $error = $authenticationUtils->getLastAuthenticationError();

    //Prepare the form for adding a new medication
    $user = $this->getUser();
    $medicinList = [];

    $medikament = new MedikamentListe(); // Create a new MedikamentListe object

    $form = $this->createForm(AddMedicinType::class, $medikament); // Create the form using the AddMedicinType class
    $form->handleRequest($request); // Handle the form submission

    if( $form->isSubmitted() && $form->isValid() ) {
      $medicinList->setUserId($user); // Set the user for the medication

      //Persist the new medication to the database
      $entityManager->persist($medikament);
      $entityManager->flush();

      return $this->redirectToRoute('medicin'); // Redirect to the medicin page after saving
    }

    //Get the current logged in user's medication list if it exists
    $medList = $user->getMedikamentListes(); // Get the user's medication list

    if ($medList->isEmpty()) {
      $medicinList = null;

    } else {
      //Create an array containing all medications sorted by name
      $medListArray = $medList->toArray();

      usort($medListArray, function ($a, $b) {
        return strcmp($a->getMedikamentNavn(), $b->getMedikamentNavn()); // Sort by medicament name
      });
    
      $medicinList = $medListArray;
    }

    return $this->render('page/medicin.html.twig', [
      'medicinList'=> $medicinList,
      'form' => $form, // Pass the form to the template
      'error' => $error,
    ]);
  }

  #[Route('/historik', name: 'historik')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function historik(
    MedikamentLogRepository $medikamnetLog,
    LoggerInterface $logger
  ): Response {
    $user = $this->getUser();
    $medicinLogs = [];

    $medLogs = $user->getMedikamentLogs(); // Get the user's medication logs
    if ($medLogs->isEmpty()) {
      $logger->warning('Der er ingen medicin logget for denne bruger!');
      //Set the medicinList to null if there are no logs
      $medicinLogs = null;
    } else {
      //Create an array containing all medication logs sorted by date
      $medLogsArray = $medLogs->toArray();

      usort($medLogsArray, function ($a, $b) {
        return $b->getTagetTid() <=> $a->getTagetTid(); // Sort by tagetTid in descending order
      });


      $medicinLogs = $medLogsArray;
    }


    // Render the template with the medication list
    return $this->render('page/historik.html.twig', [
      'medicinLogs' => $medicinLogs,
    ]);
  }

  #[Route('/udstyr', name: 'udstyr')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function udstyr(
    UdstyrRepository $udstyrRepository,
    LoggerInterface $logger
  ): Response {

    return $this->render('page/udstyr.html.twig', [
    ]);
  }

  #[Route('/hjaelp', name: 'hjaelp')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function hjaelp(): Response
  {

    return $this->render('page/hjaelp.html.twig', [
    ]);
  }

  #[Route(path: '/login', name: 'login')]
  public function login(
    Request $request,
    AuthenticationUtils $authenticationUtils,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager
  ): Response {
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();
  
    // Prepare registration form
    $user = new User();
    $registrationForm = $this->createForm(RegistrationFormType::class, $user);
  
    return $this->render('page/login.html.twig', [
      'last_username' => $lastUsername,
      'error' => $error,
      'registration_error' => null,
      'registrationForm' => $registrationForm,
    ]);
  }

  #[Route('/logout', name: 'logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }

  #[Route('/profil', name: 'profil')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function profil(): Response
  {
    $user = $this->getUser();
    $userName = $user->getFuldeNavn();


    return $this->render('page/profil.html.twig', [
    ]);
  }

  #[Route('/register', name: 'register')]
  public function register(
    Request $request,
    AuthenticationUtils $authenticationUtils,
    UserPasswordHasherInterface $userPasswordHasher,
    EntityManagerInterface $entityManager
  ): Response {
    $user = new User();
    $error = $authenticationUtils->getLastAuthenticationError();

    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      /** @var string $plainPassword */
      $plainPassword = $form->get('plainPassword')->getData();

      // encode the plain password
      $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

      $entityManager->persist($user);
      $entityManager->flush();
      return $this->redirectToRoute('home');
    }
    
    return $this->render('page/login.html.twig', [
      'registrationForm' => $form,
      'error' => null,
      'registration_error' => $error,
    ]);
  }

}
