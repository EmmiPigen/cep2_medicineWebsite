<?php
namespace App\Controller;

use App\Entity\MedikamentListe;
use App\Entity\User;
use App\Form\AddMedicinType;
use App\Form\CaregiverType;
use App\Form\ProfilBilledeType;
use App\Form\RegistrationFormType;
use App\Form\UpdateInfoType;
use App\Repository\MedikamentListeRepository;
use App\Repository\MedikamentLogRepository;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $user     = $this->getUser();
        $userName = $user->getFuldeNavn();

        if (! $user) {
            throw $this->createAccessDeniedException('Du skal være logget ind for at se denne side.');
        }

        //Check if the user has any medications for today
        $now     = new \DateTime('');
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
                $nextTime     = min($upcomingTimes);
                $tidsinterval = $med->getTimeInterval();

                $sidstTages = date("H:i", strtotime("+$tidsinterval minutes", strtotime($nextTime)));

                $nextMedications[] = [
                    'medikamentNavn' => $med->getMedikamentNavn(),
                    'tidspunktTages' => $nextTime,
                    'sidstTages'     => $sidstTages,
                    'prioritet'      => $med->getPrioritet(),
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
            'Navn'         => $userName,
            'lastLog'      => $lastLog,
            'restMedsIdag' => $restMedsIdag,
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
        $user        = $this->getUser();
        $medicinList = [];

        $medikament = new MedikamentListe(); // Create a new MedikamentListe object

        $form = $this->createForm(AddMedicinType::class, $medikament); // Create the form using the AddMedicinType class
        $form->handleRequest($request);                                // Handle the form submission

        if ($form->isSubmitted() && $form->isValid()) {
            $medikament->setUserId($user); // Set the user for the medication

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
            'medicinList' => $medicinList,
            'form'        => $form, // Pass the form to the template
            'error'       => $error,
        ]);
    }

    #[Route('/historik', name: 'historik')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function historik(
        MedikamentLogRepository $medikamnetLog,
        LoggerInterface $logger
    ): Response {
        $user        = $this->getUser();
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
    ): Response {
        $user = $this->getUser();

        $udstyr = $user->getUdstyrs(); // Get the user's udstyr list
        if ($udstyr->isEmpty()) {
            $udstyrListe = null;
        } else {
            //Create an array containing all udstyr sorted by date
            $udstyrArray = $udstyr->toArray();

            usort($udstyrArray, function ($a, $b) {
                return $b->getLokale() <=> $a->getLokale();
            });
            $udstyrListe = $udstyrArray;
        }

        return $this->render('page/udstyr.html.twig', [
            'udstyrListe' => $udstyrListe,
        ]);
    }

    #[Route('/hjaelp', name: 'hjaelp')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function hjaelp(TranslatorInterface $translator): Response
    {
        $title        = $translator->trans('help.title');
        $description1 = $translator->trans('help.description1');

        $helps = [];

        for ($i = 1; $i <= 9; $i++) {
            $helps[] = [
                'help'        => $translator->trans("help.help{$i}"),
                'description' => $translator->trans("help.help{$i}Description"),
            ];
        }

        return $this->render('page/hjaelp.html.twig', [
            'title'        => $title,
            'description1' => $description1,
            'helps'        => $helps,
        ]);
    }

    #[Route(path: '/login', name: 'login')]
    public function login(
        AuthenticationUtils $authenticationUtils,
    ): Response {
        $error        = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Prepare registration form
        $user             = new User();
        $registrationForm = $this->createForm(RegistrationFormType::class, $user);

        return $this->render('page/login.html.twig', [
            'last_username'      => $lastUsername,
            'error'              => $error,
            'registration_error' => null,
            'registrationForm'   => $registrationForm,
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/profil', name: 'profil')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profil(
        Request $request,
        EntityManagerInterface $entityManager,
        loggerInterface $logger,
        AuthenticationUtils $authenticationUtils
    ): Response {
        $error = $authenticationUtils->getLastAuthenticationError();
        $user           = $this->getUser();
        $updateInfoForm = $this->createForm(UpdateInfoType::class, $user);
        $updateInfoForm->handleRequest($request);
        if ($updateInfoForm->isSubmitted() && $updateInfoForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Profiloplysninger opdateret!');
            return $this->redirectToRoute('profil');
        }
        // Kontaktperson-formular
        $form = $this->createForm(CaregiverType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Kontaktperson opdateret!');
            return $this->redirectToRoute('profil');
        }

        // Profilbillede-formular
        $profilBilledeForm = $this->createForm(ProfilBilledeType::class);
        $profilBilledeForm->handleRequest($request);

        if ($profilBilledeForm->isSubmitted() && $profilBilledeForm->isValid()) {
            $file = $profilBilledeForm->get('profilBillede')->getData();

            if ($file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );
                $logger->info('Profilbillede uploaded: ' . $newFilename);
                $user->setProfilBillede($newFilename);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profilbillede opdateret!');
                return $this->redirectToRoute('profil');
            }
        }

        return $this->render('page/profil.html.twig', [
            'error'             => $error,
            'user'              => $user,
            'updateInfoForm'    => $updateInfoForm->createView(),
            'caregiverForm'     => $form->createView(),
            'profilBilledeForm' => $profilBilledeForm->createView(),
        ]);
    }

    #[Route('/profil/slet-billede', name: 'profil_slet_billede', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function sletProfilbillede(
        EntityManagerInterface $entityManager
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $uploadDir   = $this->getParameter('upload_directory');
        $billedePath = $uploadDir . '/' . $user->getProfilBillede();

        if ($user->getProfilBillede() && file_exists($billedePath)) {
            unlink($billedePath);
        }

        $user->setProfilBillede(null);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Profilbilledet er blevet slettet.');
        return $this->redirectToRoute('profil');
    }

    #[Route('/tjek-medicin', name: 'tjek_medicin')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function tjekMedicintider(
        EntityManagerInterface $em,
        MedikamentLogRepository $logRepo,
        SmsService $smsService // Twilio service
    ): Response {
        $user = $this->getUser();
        $now  = new \DateTime();

        foreach ($user->getMedikamentListes() as $med) {
            foreach ($med->getTidspunkterTages() as $tidspunkt) {
                // Opbyg medicinens planlagte tidspunkt
                $medTime = \DateTime::createFromFormat('H:i', $tidspunkt);
                $medTime->setDate($now->format('Y'), $now->format('m'), $now->format('d'));

                // Skip hvis tiden ikke er overskredet endnu
                if ($now < $medTime) {
                    continue;
                }

                // Tjek om medicinen er logget som "taget"
                $matchFundet = false;
                foreach ($user->getMedikamentLogs() as $log) {
                    if (
                        $log->getMedikamentNavn() === $med->getMedikamentNavn() &&
                        $log->getTagetTid()?->format('Y-m-d') === $now->format('Y-m-d') &&
                        $log->getTagetStatus() === 'taget'
                    ) {
                        $matchFundet = true;
                        break;
                    }
                }

                // Hvis intet log-match => medicinen er ikke taget → send besked
                if (! $matchFundet && $user->getOmsorgspersonTelefon()) {
                    $smsService->sendSms(
                        $user->getOmsorgspersonTelefon(),
                        'OBS: ' . $user->getFuldeNavn() . ' har ikke taget medicinen "' . $med->getMedikamentNavn() . '" kl. ' . $tidspunkt
                    );

                    $this->addFlash('success', 'Besked sendt til kontaktperson om manglende medicin.');
                }
            }
        }

        return $this->redirectToRoute('profil');
    }

    #[Route('/register', name: 'register')]
    public function register(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user  = new User();
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */// Retrieve the plain password from the form
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('page/login.html.twig', [
            'registrationForm'   => $form,
            'error'              => null,
            'registration_error' => $error,
        ]);
    }

    #[Route('/test-send-sms', name: 'test_send_sms')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function testSendSms(
        EntityManagerInterface $em,
        \App\Service\SmsService $smsService // Husk korrekt namespace
    ): Response {
        $user = $this->getUser();

        $now  = new \DateTime();
        $logs = $user->getMedikamentLogs();

        foreach ($logs as $log) {
            if (
                $log->getTagetStatus() !== 'taget' &&
                $log->getTagetTid() !== null &&
                $log->getTagetTid() < $now
            ) {
                $kontaktNavn = $user->getOmsorgspersonNavn();
                $kontaktTlf  = $user->getOmsorgspersonTelefon();

                if (! $kontaktTlf) {
                    return new Response(' Kontaktpersonens telefonnummer mangler.');
                }

                $besked = " {$user->getFuldeNavn()} har ikke taget medicinen '{$log->getMedikamentNavn()}' som planlagt.";

                try {
                    $smsService->sendSms($kontaktTlf, $besked);
                    return new Response(" SMS sendt til $kontaktNavn: $besked");
                } catch (\Exception $e) {
                    return new Response('Fejl ved SMS: ' . $e->getMessage());
                }
            }
        }

        return new Response('Ingen manglende medicin ingen SMS sendt.');
    }

    #[Route('/medicin/{id}/delete', name: 'delete_medicin', methods: ['POST'])]
    public function deleteMedicin(
        Request $request,
        MedikamentListe $medikamentListe,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $medikamentListe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($medikamentListe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('medicin', [], Response::HTTP_SEE_OTHER);
    }
}
