<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\MedikamentListe;
use App\Entity\MedikamnetLog;
use Doctrine\ORM\EntityManagerInterface;

final class MedicineController extends AbstractController
{
  //This controller is just used to create some dummy data for testing purposes.
  #[Route('/medicine', name: 'create_medicine')]
  #[IsGranted('IS_AUTHENTICATED_FULLY')]
  public function createMedicine(EntityManagerInterface $entityManager): Response
  {
    $medicineNames = ['Vitamin C', 'Aspirin', 'Ibuprofen', 'Paracetamol', 'Amoxicillin', 'Cough Syrup', 'Antihistamine', 'Probiotic', 'Omega-3', 'Zinc'];
    $units = ['mg', 'ml', 'g', 'unit'];
   
    for ($i = 0; $i < 10; $i++){
      $medicine = new Medikamentliste();
      
      $medicine->setMedikamentNavn($medicineNames[array_rand($medicineNames)]);
      $medicine->setTidspunkterTages($timetaken[array_rand($timetaken)]);
      $medicine->setTimeInterval($intervals[array_rand($intervals)]);
      $medicine->setEnhed($units[array_rand($units)]);
      $medicine->setDosis($dosage[array_rand($dosage)]);
      $medicine->setPrioritet($priorities[array_rand($priorities)]);
      
      $user = $this->getUser(); // Get the current logged in user

      $medicine->setUserId($user); // Set the user for the medicine



      $entityManager->persist($medicine);
      $entityManager->flush();
    }

    
    return new Response('Saved new medicine with id ' . $medicine->getId());
  }
}
