<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use \App\Entity\Utilisateur;

use \App\Repository\SejourRepository;
use App\Repository\SpecialiteRepository;

use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

class PatientController extends AbstractController
{
    #[Route('/patient/historique', name: 'patientHistorique', methods: ['GET'])]
    public function getHistorique(Request $request, /*Utilisateur $patient, */SejourRepository $sejourRepository): /*Json*/Response
    {
//        $id_session = $this->get('session')->get('id');
//        $id_session = $request->getSession();
//        var_dump($id_session);
        $historique = $sejourRepository->findBy(['id_patient' => $this->getUser()->getId()]);
//        return $this->json([
//            'id' => $id_session,
//            'message' => 'Welcome to your new controller!',
//            'path' => 'src/Controller/PatientController.php',
//        ]);
        
//        var_dump($historique);
        return $this->render('espace_patient.html.twig', [
            'historique' => $historique
//            'id' => $id_session,
//            'form' => $form,
        ]);
    }
    
    #[Route('/patient/profile', name: 'patientProfile', methods: ['GET'])]
    public function getProfile(Request $request) : JsonResponse
    {
        
        var_dump($this->getUser());
        var_dump($request->getSession()->getId());
        echo '<br />--------------------------<br />';
//        return $this->render('espace_patient.html.twig', [
//            'id' => $id_session,
////            'form' => $form,
//        ]);
        
        return new JsonResponse(null, Response::HTTP_OK);
    }
    
    #[Route('/patient/sejour', name: 'patientSejour', methods: ['GET', 'POST'])]
    public function newSejour(Request $request, EntityManagerInterface $entityManager, SpecialiteRepository $specialite_repository) : Response
    {
        $post_data = json_decode($request->getContent(), true);
        
        
        $sejour = new \App\Entity\Sejour();
        
        if($post_data) {
//            var_dump($post_data);
            $specialite = $specialite_repository->findOneBy(['id' => $post_data['sejour[id_specialite]']]);
            $sejour->setIdSpecialite($specialite);
        }
        
        $form = $this->createForm(\App\Form\SejourType::class, $sejour, ['action' => $this->generateUrl('patientSejour')]);
        
        $form->handleRequest($request);
        
         if ($form->isSubmitted() && $form->isValid()) {
//             var_dump($request);
             $data = $form->getData();
             
             $sejour->setIdPatient($this->getUser());
             
//             var_dump($sejour);
            $entityManager->persist($sejour);
            $entityManager->flush();
            
        } elseif ($request->isXmlHttpRequest()) {
            echo 'isXmlHttpRequest';
            echo $request->get('status');
//            die();
        } else {
//            var_dump($request);
        }
        
        return $this->render('sejour.html.twig', [
         'form' => $form->createView(),   
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? 422 : 200));
//        var_dump($this->getUser());
//        var_dump($request->getSession()->getId());
//        echo '<br />--------------------------<br />';
//        return $this->render('espace_patient.html.twig', [
//            'id' => $id_session,
////            'form' => $form,
//        ]);
        
//        return new JsonResponse(null, Response::HTTP_OK);
    }
    
//    #[Route('/patient/sejour', name: 'createPatientSejour', methods: ['POST'])]
//    public function createSejour(Request $request) : JsonResponse
//    {
//        
//        var_dump($this->getUser());
//        var_dump($request->getSession()->getId());
//        echo '<br />--------------------------<br />';
//        return $this->render('espace_patient.html.twig', [
//            'id' => $id_session,
////            'form' => $form,
//        ]);
        
//        return new JsonResponse(null, Response::HTTP_OK);
//    }
}
