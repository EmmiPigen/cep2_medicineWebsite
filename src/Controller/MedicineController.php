<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Medikament;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MedikamentRepository;

final class MedicineController extends AbstractController
{
    #[Route('/medicine/{id}', name: 'create_medicine')]
    public function createMedicine(MedikamentRepository $MedikamentRepository, int $id): Response
    {
        /* $medicine = $entityManager->getRepository(Medikament::class)->find($id);

        if (!$medicine) {
            throw $this->createNotFoundException(
                'No medicine found for id '.$id
            );
        } */
        
        $medicine = $MedikamentRepository
          ->find($id);
        
        return new Response('Check out the medication: '.$medicine->getName());
    }
}


/* $medicine = new Medikament();
$medicine->setName('Ibuprofen');
$medicine->setDosis(400);
$medicine->setPriority(1);
$medicine->setTimeInterval(6);
$medicine->setAmount(1);
$medicine->setTimeTaken(new \DateTime('now'));

//Tell doctrine you want to eventually save the medicine
$entityManager->persist($medicine);

//Actually save the medicine
$entityManager->flush(); */