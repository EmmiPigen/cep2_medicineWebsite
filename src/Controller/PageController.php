<?php
namespace App\Controller;

use App\Entity\Udstyr;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\MedikamentListeRepository;
use App\Repository\MedikamentLogRepository;
use App\Repository\MedikamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
        $userName = $user->getFuldeNavn();

        if (! $user) {
            throw $this->createAccessDeniedException('Du skal være logget ind for at se denne side.');
        }

        $medLogs = $user->getMedikamentLogs(); // Get the user's medication logs

        //Getting the next two medications the user have to take based on the current time
        $now     = new \DateTime();
        $nowTime = $now->format('H:i');

        $nextMedications = [];
        foreach ($user->getMedikamentListes() as $med) {
            $times = $med->getTidspunkterTages();

            //Filter times that are still ahead today

            $upcomingTimes = array_filter($times, function ($time) use ($nowTime) {
                return $time >= $nowTime;
            });

            // get the soonest time
            if (! empty($upcomingTimes)) {
                $nextTime = min($upcomingTimes);

                $nextMedications[] = [
                    'medikamentNavn' => $med->getMedikamentNavn(),
                    'tidspunktTages' => $nextTime,
                    'tidsinterval'   => $med->getTimeInterval(),
                ];
            }
        }

        //Sort the soonest upcoming times
        usort($nextMedications, fn($a, $b) => strcmp($a['tidspunktTages'], $b['tidspunktTages']));

        $nextMed = $nextMedications[0] ?? null; // Get the first upcoming medication
        
        

        if ($medLogs->isEmpty()) {
            $logger->warning('Der er ingen medicin logget for denne bruger!');
            // Handle the case when there are no logs for the user
            $lastLog = [
                'medikamentNavn' => 'Ingen medicin logget',
                'tagetTid'       => 'Ingen tid logget',
                'tagetStatus'    => true,
                'tagetLokale'    => 'Ingen lokation logget',
            ];

        } else {
            $lastLog = $logs->last(); // Get the last log entry
        }

        //Render the template with the medication data
        return $this->render('page/home.html.twig', [
            'lastLog' => $lastLog,
            'Navn'   => $userName,]);
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
                'message'     => 'Ingen medicin tilgængelig',
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
            'error'         => $error,
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
