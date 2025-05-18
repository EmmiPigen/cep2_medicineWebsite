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
    public function home(MedikamentLogRepository $medikamentLog, MedikamentListeRepository $medikamentListe, LoggerInterface $logger): Response
    {
        $user = $this->getUser();
        $userName = $user->getFuldeNavn();
        $now = new \DateTime();
        $nowTime = $now->format('H:i');

        $nextMedications = [];

        foreach ($user->getMedikamentListes() as $med) {
            $times = $med->getTidspunkterTages();
            $upcomingTimes = array_filter($times, fn($time) => $time >= $nowTime);
            if (!empty($upcomingTimes)) {
                $nextTime = min($upcomingTimes);
                $sidstTages = date("H:i", strtotime("+{$med->getTimeInterval()} minutes", strtotime($nextTime)));

                $nextMedications[] = [
                    'medikamentNavn' => $med->getMedikamentNavn(),
                    'tidspunktTages' => $nextTime,
                    'sidstTages' => $sidstTages,
                    'prioritet' => $med->getPrioritet(),
                ];
            }
        }

        usort($nextMedications, fn($a, $b) => strcmp($a['tidspunktTages'], $b['tidspunktTages']));
        $restMedsIdag = array_filter($nextMedications, fn($med) => $med['tidspunktTages'] >= $nowTime);
        $lastLog = $user->getMedikamentLogs()->last() ?: null;

        return $this->render('page/home.html.twig', [
            'Navn' => $userName,
            'lastLog' => $lastLog,
            'restMedsIdag' => $restMedsIdag,
        ]);
    }

    #[Route('/profil', name: 'profil')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profil(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $user = $this->getUser();

        $updateInfoForm = $this->createForm(UpdateInfoType::class, $user);
        $updateInfoForm->handleRequest($request);

        if ($updateInfoForm->isSubmitted() && $updateInfoForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Profiloplysninger opdateret!');
            return $this->redirectToRoute('profil');
        }

        $form = $this->createForm(CaregiverType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Kontaktperson opdateret!');
            return $this->redirectToRoute('profil');
        }

        $profilBilledeForm = $this->createForm(ProfilBilledeType::class);
        $profilBilledeForm->handleRequest($request);

        if ($profilBilledeForm->isSubmitted() && $profilBilledeForm->isValid()) {
            $file = $profilBilledeForm->get('profilBillede')->getData();
            if ($file) {
                $newFilename = uniqid() . '.' . $file->guessExtension();
                $file->move($this->getParameter('upload_directory'), $newFilename);
                $logger->info('Profilbillede uploaded: ' . $newFilename);

                $user->setProfilBillede($newFilename);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profilbillede opdateret!');
                return $this->redirectToRoute('profil');
            }
        }

        return $this->render('page/profil.html.twig', [
            'error' => $error,
            'user' => $user,
            'updateInfoForm' => $updateInfoForm->createView(),
            'caregiverForm' => $form->createView(),
            'profilBilledeForm' => $profilBilledeForm->createView(),
        ]);
    }

    #[Route('/profil/slet-billede', name: 'profil_slet_billede', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function sletProfilbillede(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $path = $this->getParameter('upload_directory') . '/' . $user->getProfilBillede();

        if ($user->getProfilBillede() && file_exists($path)) {
            unlink($path);
        }

        $user->setProfilBillede(null);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Profilbilledet er blevet slettet.');
        return $this->redirectToRoute('profil');
    }

    #[Route('/tjek-medicin', name: 'tjek_medicin')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function tjekMedicintider(EntityManagerInterface $em, MedikamentLogRepository $logRepo, SmsService $smsService): Response
    {
        $user = $this->getUser();
        $now = new \DateTime();

        foreach ($user->getMedikamentListes() as $med) {
            foreach ($med->getTidspunkterTages() as $tidspunkt) {
                $medTime = \DateTime::createFromFormat('H:i', $tidspunkt);
                $medTime->setDate((int)$now->format('Y'), (int)$now->format('m'), (int)$now->format('d'));

                if ($now < $medTime) continue;

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

                if (!$matchFundet && $user->getOmsorgspersonTelefon()) {
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

    #[Route('/medicin', name: 'medicin')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function medicin(EntityManagerInterface $entityManager, AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $user = $this->getUser();
        $medikament = new MedikamentListe();
        $form = $this->createForm(AddMedicinType::class, $medikament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $medikament->setUserId($user);
            $entityManager->persist($medikament);
            $entityManager->flush();
            return $this->redirectToRoute('medicin');
        }

        $medList = $user->getMedikamentListes();
        $medicinList = $medList->isEmpty() ? null : $medList->toArray();

        return $this->render('page/medicin.html.twig', [
            'medicinList' => $medicinList,
            'form' => $form,
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/historik', name: 'historik')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function historik(MedikamentLogRepository $medikamnetLog, LoggerInterface $logger): Response
    {
        $user = $this->getUser();
        $logs = $user->getMedikamentLogs();
        $medicinLogs = $logs->isEmpty() ? null : $logs->toArray();

        if ($medicinLogs) {
            usort($medicinLogs, fn($a, $b) => $b->getTagetTid() <=> $a->getTagetTid());
        }

        return $this->render('page/historik.html.twig', [
            'medicinLogs' => $medicinLogs,
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

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

    #[Route('/register', name: 'register')]
    public function register(Request $request, AuthenticationUtils $authenticationUtils, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $error = $authenticationUtils->getLastAuthenticationError();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
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
