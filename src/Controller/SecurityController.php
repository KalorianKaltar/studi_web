<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        
        $custom_error = '';
        
        if ($this->getUser()) {
             if($this->getUser()->getIdType()->getLabel() == 'Patient') {
                return $this->redirectToRoute('patientHistorique');
             } elseif($this->getUser()->getIdType()->getLabel() == 'Administrateur') {
                 return $this->redirectToRoute('adminIndex');
             } else {
                 return $this->redirectToRoute('home');
             }
         }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'custom_error' => $custom_error]);
    }
    
    #[Route(path: '/api/login', name: 'app_api_login', methods: ['POST'])]
    public function apiLogin(AuthenticationUtils $authenticationUtils): JsonResponse
    {
//        var_dump($authenticationUtils);
         if ($this->getUser()) {
             if($this->getUser()->getIdType()->getLabel() == 'Médecin') {
                    $list = $utilisateurRepository->findAllMedecins();
                    $jsonMedecinList = $serializer->serialize($list, 'json');
        
                    return new JsonResponse('MEDECIN', Response::HTTP_OK, ['MEDECIN'], true);
             } elseif($this->getUser()->getIdType()->getLabel() == 'Secrétaire') {
                    $list = $utilisateurRepository->findAllMedecins();
                    $jsonMedecinList = $serializer->serialize($list, 'json');
        
                    return new JsonResponse($jsonMedecinList, Response::HTTP_OK, ['SECRETAIRE'], true);
             } else {
                 return new JsonResponse("NO AUTHORIZED TYPE", Response::HTTP_OK, 'NO AUTHORIZED TYPE');
             }
         } else {
             return new JsonResponse("NO USER", Response::HTTP_OK, ['NO USER']);
         }
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
