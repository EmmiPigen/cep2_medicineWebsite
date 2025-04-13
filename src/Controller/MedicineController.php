<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\MedikamentLog;
use Doctrine\ORM\EntityManagerInterface;

final class MedicineController extends AbstractController
{
  //This controller is just used to create some dummy data for testing purposes.
  #[Route('/medicine/', name: 'create_medicine')]
  public function createMedicine(EntityManagerInterface $entityManager): Response
  {
    $medicineNames = ['Vitamin C', 'Aspirin', 'Ibuprofen', 'Paracetamol', 'Amoxicillin', 'Cough Syrup', 'Antihistamine', 'Probiotic', 'Omega-3', 'Zinc'];
    $units = ['µg', 'mg', 'g', 'ml'];

    for ($i = 0; $i < 10; $i++){
      $medicine = new MedikamentLog();
      $medicine->


      $medicine->setName($medicineNames[array_rand($medicineNames)]);
      $medicine->setDosis(random_int(1, 1000));
      $medicine->setUnit($units[array_rand($units)]);
      $medicine->setPriority(random_int(1, 3));
      $medicine->setTimeInterval(random_int(1, 24));
      $medicine->setAmount(random_int(1, 5));
      $medicine->setTimeTaken(new \DateTime('now - ' . rand(0, 48) . ' hours'));
      
      $entityManager->persist($medicine);

      $entityManager->flush();
    }

    
    return new Response('Saved new medicine with id ' . $medicine->getId());
  }
}
