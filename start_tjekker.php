<?php

use App\Kernel;
use App\Background\MedicintjekRunner;
use Symfony\Component\Dotenv\Dotenv;

require_once 'vendor/autoload.php';

// Indlæs miljøvariabler
$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');
if (file_exists(__DIR__.'/.env.local')) {
    $dotenv->load(__DIR__.'/.env.local');
}

// Start Symfony-kernen
$kernel = new Kernel('dev', true);
$kernel->boot();
$container = $kernel->getContainer();

// Hent og kør baggrundstjek
$runner = $container->get(MedicintjekRunner::class);
$runner->run();
