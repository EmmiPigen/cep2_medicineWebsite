<?php

namespace App\Controller\Api;

use App\Entity\MedikamentLog;
use App\Entity\User;
use App\Repository\MedikamentListe;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name:'api')]
class MedikamentLogApiController extends AbstractController
{
  
}