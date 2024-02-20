<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
     private UserPasswordHasherInterface $hasher;
     
     public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        // SPECIALITE
        
        $specialite_chir = new \App\Entity\Specialite;
        $specialite_chir->setLabel("Chirurgie");        
        $manager->persist($specialite_chir);
        
        
        $specialite_radi = new \App\Entity\Specialite;
        $specialite_radi->setLabel("Radiologie");        
        $manager->persist($specialite_radi);
        
        
        $specialite_neur = new \App\Entity\Specialite;
        $specialite_neur->setLabel("Neurologie");        
        $manager->persist($specialite_neur);
        
        
        $specialite_onco = new \App\Entity\Specialite;
        $specialite_onco->setLabel("Oncologie");        
        $manager->persist($specialite_onco);
        
        
        // TYPE UTILISATEUR
        
        $type_utilisateur_adm = new \App\Entity\TypeUtilisateur;
        $type_utilisateur_adm->setLabel("Administrateur");        
        $manager->persist($type_utilisateur_adm);
        
        
        $type_utilisateur_med = new \App\Entity\TypeUtilisateur;
        $type_utilisateur_med->setLabel("Médecin");        
        $manager->persist($type_utilisateur_med);
        
        
        $type_utilisateur_sec = new \App\Entity\TypeUtilisateur;
        $type_utilisateur_sec->setLabel("Secrétaire");        
        $manager->persist($type_utilisateur_sec);
        
        
        $type_utilisateur_pat = new \App\Entity\TypeUtilisateur;
        $type_utilisateur_pat->setLabel("Patient");        
        $manager->persist($type_utilisateur_pat);
        
        
        // UTILISATEUR
        
        $utilisateur_adm = new \App\Entity\Utilisateur;
        $utilisateur_adm->setPrenom("Adm");
        $utilisateur_adm->setNom("Nistrateur");
        $utilisateur_adm->setRoles(['ROLE_ADMIN']);
        $utilisateur_adm->setIdType($type_utilisateur_adm);        
        $utilisateur_adm->setEmail(strtolower(substr($utilisateur_adm->getPrenom(), 0, 1)) . "." . strtolower(substr($utilisateur_adm->getNom(), 0, 1)) . "@studisoignemoi.fr");
        $utilisateur_adm->setPassword($this->hasher->hashPassword($utilisateur_adm, 'temp_123'));
        $manager->persist($utilisateur_adm);
        
        
        $utilisateur_sec = new \App\Entity\Utilisateur;
        $utilisateur_sec->setPrenom("Sec");
        $utilisateur_sec->setNom("Rétaire");
        $utilisateur_sec->setRoles(['ROLE_SECRETAIRE']);
        $utilisateur_sec->setIdType($type_utilisateur_sec);        
        $utilisateur_sec->setEmail(strtolower(substr($utilisateur_sec->getPrenom(), 0, 1)) . "." . strtolower(substr($utilisateur_sec->getNom(), 0, 1)) . "@studisoignemoi.fr");
        $utilisateur_sec->setPassword($this->hasher->hashPassword($utilisateur_sec, 'temp_123'));
        $manager->persist($utilisateur_sec);
        
        
        $utilisateur_med_chir = new \App\Entity\Utilisateur;
        $utilisateur_med_chir->setPrenom("Jean");
        $utilisateur_med_chir->setNom("Chirurgien");
        $utilisateur_med_chir->setRoles(['ROLE_MEDECIN']);
        $utilisateur_med_chir->setIdType($type_utilisateur_med);
        $utilisateur_med_chir->setEmail(strtolower(substr($utilisateur_med_chir->getPrenom(), 0, 1)) . "." . strtolower(substr($utilisateur_med_chir->getNom(), 0, 1)) . "@studisoignemoi.fr");
        $utilisateur_med_chir->setIdSpecialite($specialite_chir);
        $utilisateur_med_chir->setMatricule(substr($specialite_chir->getLabel(), 0, 1) . "000000001");
        $utilisateur_med_chir->setPassword($this->hasher->hashPassword($utilisateur_med_chir, 'temp_123'));
        $manager->persist($utilisateur_med_chir);
        
        
        $utilisateur_med_neur = new \App\Entity\Utilisateur;
        $utilisateur_med_neur->setPrenom("Jean");
        $utilisateur_med_neur->setNom("Neurologue");
        $utilisateur_med_neur->setRoles(['ROLE_MEDECIN']);
        $utilisateur_med_neur->setIdType($type_utilisateur_med);
        $utilisateur_med_neur->setEmail(strtolower(substr($utilisateur_med_neur->getPrenom(), 0, 1)) . "." . strtolower(substr($utilisateur_med_neur->getNom(), 0, 1)) . "@studisoignemoi.fr");
        $utilisateur_med_neur->setIdSpecialite($specialite_neur);
        $utilisateur_med_neur->setMatricule(substr($specialite_neur->getLabel(), 0, 1) . "000000001");
        $utilisateur_med_neur->setPassword($this->hasher->hashPassword($utilisateur_med_neur, 'temp_123'));
        $manager->persist($utilisateur_med_neur);
        
        
        $utilisateur_med_onco = new \App\Entity\Utilisateur;
        $utilisateur_med_onco->setPrenom("Jean");
        $utilisateur_med_onco->setNom("Oncologue");
        $utilisateur_med_onco->setRoles(['ROLE_MEDECIN']);
        $utilisateur_med_onco->setIdType($type_utilisateur_med);
        $utilisateur_med_onco->setEmail(strtolower(substr($utilisateur_med_onco->getPrenom(), 0, 1)) . "." . strtolower(substr($utilisateur_med_onco->getNom(), 0, 1)) . "@studisoignemoi.fr");
        $utilisateur_med_onco->setIdSpecialite($specialite_onco);
        $utilisateur_med_onco->setMatricule(substr($specialite_onco->getLabel(), 0, 1) . "000000001");
        $utilisateur_med_onco->setPassword($this->hasher->hashPassword($utilisateur_med_onco, 'temp_123')); 
        $manager->persist($utilisateur_med_onco);
        
        
        $utilisateur_med_radi = new \App\Entity\Utilisateur;
        $utilisateur_med_radi->setPrenom("Jean");
        $utilisateur_med_radi->setNom("Radiologue");
        $utilisateur_med_radi->setRoles(['ROLE_MEDECIN']);
        $utilisateur_med_radi->setIdType($type_utilisateur_med);
        $utilisateur_med_radi->setEmail(strtolower(substr($utilisateur_med_radi->getPrenom(), 0, 1)) . "." . strtolower(substr($utilisateur_med_radi->getNom(), 0, 1)) . "@studisoignemoi.fr");
        $utilisateur_med_radi->setIdSpecialite($specialite_radi);
        $utilisateur_med_radi->setMatricule(substr($specialite_radi->getLabel(), 0, 1) . "000000001");
        $utilisateur_med_radi->setPassword($this->hasher->hashPassword($utilisateur_med_radi, 'temp_123'));
        $manager->persist($utilisateur_med_radi);
        
        
        $utilisateur_pat_chir = new \App\Entity\Utilisateur;
        $utilisateur_pat_chir->setPrenom("Jean");
        $utilisateur_pat_chir->setNom("Patience");
        $utilisateur_pat_chir->setRoles(['ROLE_PATIENT']);
        $utilisateur_pat_chir->setIdType($type_utilisateur_pat);
//        $utilisateur_pat_chir->setEmail(strtolower(substr($utilisateur_pat_chir->getPrenom(), 0, 1)) . "." . strtolower(substr($utilisateur_pat_chir->getNom(), 0, 1)) . "@studisoignemoi.fr");
        $utilisateur_pat_chir->setEmail("jp@test.fr");
        $utilisateur_pat_chir->setAdresse("1 rue Gosselet");
        $utilisateur_pat_chir->setAdresseComplement("");
        $utilisateur_pat_chir->setCodePostal(59350);
        $utilisateur_pat_chir->setPassword($this->hasher->hashPassword($utilisateur_pat_chir, 'temp_123'));
        $manager->persist($utilisateur_pat_chir);
        
        
        // SEJOUR
        
        $sejour_pat_chir = new \App\Entity\Sejour;
        $sejour_pat_chir->setIdPatient($utilisateur_pat_chir);
        $sejour_pat_chir->setIdSpecialite($specialite_chir);
        $sejour_pat_chir->setIdMedecin($utilisateur_med_chir);
        
        $date_debut = \DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s"));
        $date_fin = clone $date_debut;
        $date_fin = $date_fin->add(new \DateInterval("P7D"));
        
        $sejour_pat_chir->setDateDebut($date_debut);
        $sejour_pat_chir->setDateFin($date_fin);
        $sejour_pat_chir->setMotif("Douleur abdominale");
        $manager->persist($sejour_pat_chir);
        
        // AVIS
        $avis_pat_chir = new \App\Entity\Avis;
        $avis_pat_chir->setDate(\DateTime::createFromFormat("Y-m-d H:i:s", date("Y-m-d H:i:s")));
        $avis_pat_chir->setLibelle("Déchirure abdominale");
        $avis_pat_chir->setDescription("A recoudre");
        $avis_pat_chir->setIdMedecin($utilisateur_med_chir);
        $avis_pat_chir->setIdSejour($sejour_pat_chir);
        $manager->persist($avis_pat_chir);
        
        // PRESCRIPTION
        $prescription_pat_chir = new \App\Entity\Prescription;
        $prescription_pat_chir->setIdSejour($sejour_pat_chir);
        $prescription_pat_chir->setDateDebut($date_debut);
        $prescription_pat_chir->setDateFin($date_fin);
        $prescription_pat_chir->setMedicament("Antidouleur");
        $prescription_pat_chir->setPosologie("1 matin - 1 soir");
        $prescription_pat_chir->setIdMedecin($utilisateur_med_chir);
        $manager->persist($prescription_pat_chir);
        
        
        $manager->flush();
    }
}
