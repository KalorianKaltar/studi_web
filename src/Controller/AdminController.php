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

use Doctrine\Persistence\ManagerRegistry;


use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use \App\Entity\Utilisateur;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'adminIndex', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository) : Response {
        
        $medecins = $utilisateurRepository->findAllMedecins();
            return $this->render('admin.html.twig', [
            'medecins' => $medecins
        ]);
    }
    
    #[Route('/admin/medecin/{id}', name: 'adminDashboard', methods: ['GET'])]
    public function medecinDashboard(Request $request, UtilisateurRepository $utilisateurRepository) : Response {
        
//        $medecins = $utilisateurRepository->findAllMedecins();
            return $this->render('admin_medecin_dashboard.html.twig', [
//            'medecins' => $medecins
        ]);
    }
    
    #[Route('/admin/medecins', name: 'medecins', methods: ['GET', 'POST'])]
    public function newMedecin(Request $request, EntityManagerInterface $entityManager, UtilisateurRepository $utilisateur_repository, UserPasswordHasherInterface $passwordHasher) : Response
    {   
        $medecin = new \App\Entity\Utilisateur();
        
        $form = $this->createForm(\App\Form\MedecinType::class, $medecin, ['action' => $this->generateUrl('medecins')]);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            $medecin->setRoles(['ROLE_MEDECIN']);
            
            $medecin->setPassword($passwordHasher->hashPassword($medecin, 'temp_123'));
            
            $medecin->setIdType($entityManager->getRepository('\App\Entity\TypeUtilisateur')->findOneBy(['label' => 'Médecin']));
            
            $specialite = $medecin->getIdSpecialite();
            
            // Matricule
            
            $matricule_max = $entityManager->getRepository('\App\Entity\Utilisateur')->findMaxMatriculeBySpecialite($specialite);

            if($matricule_max) {
                $matricule_max++;
            } else {
                return new JsonResponse(null, Response::HTTP_NOT_FOUND);
            }

            $matricule = substr($specialite->getLabel(), 0, 1) . sprintf('%09d', $matricule_max);        
            $medecin->setMatricule($matricule);

            // Email

            $email = $this->isEmailAvailable($entityManager->getRepository('\App\Entity\Utilisateur'), $medecin);
            $medecin->setEmail($email);
            
            
            $entityManager->persist($medecin);
            $entityManager->flush();
        }
        
        return $this->render('admin_medecins.html.twig', [
         'form' => $form   
        ]);
    }
    
    #[Route('/api/admin/medecins', name: 'medecin', methods: ['GET'])]
    public function getMedecinsList(UtilisateurRepository $utilisateurRepository, SerializerInterface $serializer): JsonResponse
    {
        $list = $utilisateurRepository->findAllMedecins();
        $jsonMedecinList = $serializer->serialize($list, 'json');
        
        return new JsonResponse($jsonMedecinList, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/admin/roles', name: 'roles', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour voir les roles du profile')]
    public function getCurrentRoles(SerializerInterface $serializer) : JsonResponse
    {
        $roles = $serializer->serialize($this->getUser()->getRoles(), 'json');
        
        return new JsonResponse($roles, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/admin/medecins/{id}', name: 'detailMedecin', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour voir le profile du médecin')]
    
    public function getDetailMedecin(Utilisateur $utilisateur, ManagerRegistry $managerRepository, SerializerInterface $serializer): JsonResponse
    {
        $type_medecin = $managerRepository->getRepository('\App\Entity\TypeUtilisateur')->findOneBy(['label' => 'Médecin']);
        
        if($type_medecin->getId() == $utilisateur->getIdType()->getId()) {
            $jsonMedecin = $serializer->serialize($utilisateur, 'json');
            return new JsonResponse($jsonMedecin, Response::HTTP_OK, [], true);
        } else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
    }
    
    #[Route('/api/admin/medecins', name: 'createMedecin', methods: ['POST'])]
    public function createMedecin(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, \App\Repository\SpecialiteRepository $specialiteRepository, UserPasswordHasherInterface $passwordHasher) : JsonResponse
    {
        $medecin = $serializer->deserialize($request->getContent(), Utilisateur::class, 'json');
        
        // Récupération de l'ensemble des données envoyées sous forme de tableau
        $content = $request->toArray();

        // Récupération de l'idSpecialite. S'il n'est pas défini, alors on met -1 par défaut.
        $idSpecialite = $content['idSpecialite'] ?? -1;
        
        // On cherche la specialite qui correspond et on l'assigne au médecin.
        // Si "find" ne trouve pas la specialite, alors null sera retourné.
        $specialite = $specialiteRepository->find($idSpecialite);
        
        $medecin->setIdSpecialite($specialite);
        
        $medecin->setIdType($em->getRepository('\App\Entity\TypeUtilisateur')->findOneBy(['label' => 'Médecin']));
        
        // Matricule
        
        $matricule_max = $em->getRepository('\App\Entity\Utilisateur')->findMaxMatriculeBySpecialite($specialite);
        
        if($matricule_max) {
            $matricule_max++;
        } else {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }
        
        $matricule = substr($specialite->getLabel(), 0, 1) . sprintf('%09d', $matricule_max);        
        $medecin->setMatricule($matricule);
        
        // Email
        
        $email = $this->isEmailAvailable($em->getRepository('\App\Entity\Utilisateur'), $medecin);
        $medecin->setEmail($email);
        
        
        
        $medecin->setPassword($passwordHasher->hashPassword($medecin, 'temp_123'));
        
        $em->persist($medecin);
        $em->flush();

        $jsonMedecin = $serializer->serialize($medecin, 'json');
        
        $location = $urlGenerator->generate('detailMedecin', ['id' => $medecin->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
//        $location = $urlGenerator->generate('detailMedecin', ['id' => 1], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonMedecin, Response::HTTP_CREATED, ["Location" => $location], true);
    }
    
    private function isEmailAvailable($utilisateurRepository, $medecin, $index = 1) {
        $email = strtolower(substr($medecin->getPrenom(), 0, 1)) . '.' . strtolower(substr($medecin->getNom(), 0, 1));
        
        if($index != 1) {
            $email .= "." . $index;
        }
        
        $email .= '@studisoignemoi.fr';
        
        $emailNotAvailable = $utilisateurRepository->findOneBy(['email' => $email]);
        
        if($emailNotAvailable) {
            $index++;
            $this->isEmailAvailable($utilisateurRepository, $medecin, $index);
        } else {
            return $email;
        }
    }
}
