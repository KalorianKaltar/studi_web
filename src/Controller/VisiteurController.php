<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VisiteurController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
//        return $this->json([
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/VisiteurController.php',
//        ]);
        return $this->render('home.html.twig');
//        return $this->render('base.html.twig');
    }
}
