<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\HttpFoundation\Response;

use \App\Repository\UtilisateurRepository;
use \App\Repository\EDTRepository;

use Doctrine\Persistence\ManagerRegistry;


use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use \App\Entity\EDT;
use \App\Entity\Prescription;
use \App\Entity\Avis;


use \App\Repository\PrescriptionRepository;
use \App\Repository\AvisRepository;
use \App\Repository\SejourRepository;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/secretaire')]
#[IsGranted('ROLE_SECRETAIRE', message: 'Vous n\'avez pas les droits suffisants')]
class SecretaireController extends AbstractController
{
    #[Route('/', name: 'secretaireIndex', methods: ['GET'])]
    public function index(SejourRepository $sejourRepository, UtilisateurRepository $utilisateurRepository, SerializerInterface $serializer) : JsonResponse {
        
        $secretaire = $utilisateurRepository->find($this->getUser());
         
        $json = $serializer->serialize($secretaire, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
     #[Route('/entree-sortie', name: 'secretaireEntreeSortieJour', methods: ['GET'])]
     public function entree_sortie(SejourRepository $sejourRepository, UtilisateurRepository $utilisateurRepository, SerializerInterface $serializer) : JsonResponse {
        
        $sejours = $sejourRepository->findAllByToday();
                
        $json = $serializer->serialize($sejours, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
    #[Route('/detail-patient/{id}', name: 'secretaireDetailPatient', methods: ['GET'])]
     public function detailPatient(int $id, AvisRepository $avisRepository, PrescriptionRepository $prescriptionRepository, SejourRepository $sejourRepository, UtilisateurRepository $utilisateurRepository, SerializerInterface $serializer) : JsonResponse {
        
        $patient = $utilisateurRepository->find($id);
                ;
        if($patient != null) {
            if($patient->getIdType()->getLabel() != 'Patient') {
                return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
            }
            
            $sejours = $sejourRepository->findBy(['id_patient' => $id]);
            
            $dossier = array();
            
            foreach($sejours as $sejour) {
                $dossier[$sejour->getId()]['sejour'] = $sejour;
                
                $avis = $avisRepository->findBy(['id_sejour' => $sejour->getId()]);
                
                $dossier[$sejour->getId()]['avis'] = $avis;
                
                $prescriptions = $prescriptionRepository->findBy(['id_sejour' => $sejour->getId()]);
                
                $dossier[$sejour->getId()]['prescriptions'] = $prescriptions;
            }
            
            
            
            
            $json = $serializer->serialize($dossier, 'json');

            return new JsonResponse($json, Response::HTTP_OK, [], true);
        }
        
        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);        
    }
    
    
}
