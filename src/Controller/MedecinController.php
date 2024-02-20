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

#[Route('/api/medecin')]
#[IsGranted('ROLE_MEDECIN', message: 'Vous n\'avez pas les droits suffisants')]
class MedecinController extends AbstractController
{
    #[Route('/', name: 'medecinIndex', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository, SerializerInterface $serializer) : JsonResponse {
        
        $medecin = $utilisateurRepository->find($this->getUser());
        
        $json = $serializer->serialize($medecin, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
    #[Route('/edts', name: 'medecinEDTs', methods: ['GET'])]
    public function getEDTs(EDTRepository $edtRepository, SerializerInterface $serializer) :JsonResponse
    {
        $date = new \DateTime('today');
        $edts = $edtRepository->findBy(['id_medecin' => $this->getUser(), 'date' => $date]);
        $json = $serializer->serialize($edts, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
    #[Route('/prescription', name: 'medecinAllPrescription', methods: ['GET'])]
    public function getAllPrescription(Request $request, PrescriptionRepository $prescriptionRepository, SerializerInterface $serializer) : JsonResponse
    {
        $prescription = $prescriptionRepository->findAll();
        $json = $serializer->serialize($prescription, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
    #[Route('/prescription/{id}', name: 'medecinPrescription', methods: ['GET'])]
    public function getPrescription(int $id, Request $request, PrescriptionRepository $prescriptionRepository, SerializerInterface $serializer) : JsonResponse
    {
        $prescription = $prescriptionRepository->findBy(['id_sejour' => $id]);
        $json = $serializer->serialize($prescription, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
    #[Route('/prescription', name: 'createMedecinPrescription', methods: ['POST'])]
    public function createPrescription(Request $request, EntityManagerInterface $entityManager, SejourRepository $sejourRepository, PrescriptionRepository $prescriptionRepository, SerializerInterface $serializer) : JsonResponse
    {
        $json = "";
        if($this->getUser()) {
            $prescription = $serializer->deserialize($request->getContent(), Prescription::class, 'json');
            
            $post_data = json_decode($request->getContent(), true);
            
            if($prescription) {
                $prescription->setDateDebut(new \DateTime('today'));
                $prescription->setDateFin(new \DateTime($post_data['dateFin']));
                $prescription->setIdSejour($sejourRepository->find($post_data['idSejour']));
                $prescription->setIdMedecin($this->getUser());
                
                if($prescription->getIdSejour() != null
                    && $prescription->getDateFin() != null
                    && $prescription->getPosologie() != null
                    && $prescription->getMedicament() != null)
                {
                    $entityManager->persist($prescription);
                    $entityManager->flush();
                    
                    return new JsonResponse(null, Response::HTTP_OK);
                }
            }
            
            $json = $serializer->serialize($prescription, 'json');
        }
        
        return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
    }
    
    #[Route('/avis', name: 'medecinAllAvis', methods: ['GET'])]
    public function getAllAvis(AvisRepository $avisRepository, SerializerInterface $serializer) : JsonResponse
    {
        $avis = $avisRepository->findAll();
        $json = $serializer->serialize($avis, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
    #[Route('/avis/{id}', name: 'medecinAvis', methods: ['GET'])]
    public function getAvis(int $id, AvisRepository $avisRepository, SerializerInterface $serializer) : JsonResponse
    {
        $avis = $avisRepository->findBy(["id_sejour" => $id]);
        $json = $serializer->serialize($avis, 'json');
        
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
    
    #[Route('/avis', name: 'createMedecinAvis', methods: ['POST'])]
    public function createMedecinAvis(Request $request, EntityManagerInterface $entityManager, SejourRepository $sejourRepository, AvisRepository $avisRepository, SerializerInterface $serializer) : JsonResponse
    {
        $json = "";
        if($this->getUser()) {
            $avis = $serializer->deserialize($request->getContent(), Avis::class, 'json');
            
            $post_data = json_decode($request->getContent(), true);
            
            if($avis) {
                $avis->setDate(new \DateTime('today'));
                $avis->setIdSejour($sejourRepository->find($post_data['idSejour']));
                $avis->setIdMedecin($this->getUser());
                
                if($avis->getIdSejour() != null
                    && $avis->getLibelle() != null
                    && $avis->getDescription() != null)
                {
                    $entityManager->persist($avis);
                    $entityManager->flush();
                    
                    return new JsonResponse(null, Response::HTTP_OK);
                }
            }
            
            $json = $serializer->serialize($avis, 'json');
        }
        
        return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
    }
    
}
