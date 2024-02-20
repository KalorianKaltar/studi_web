<?php

namespace App\Controller;

use App\Entity\EDT;
use App\Form\EDTType;
use App\Form\CollectionEDTType;
use App\Repository\EDTRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Sejour;
use App\Entity\CollectionEDT;

use Doctrine\Common\Collections\ArrayCollection;

#[Route('/e/d/t')]
class EDTController extends AbstractController
{
    
    #[Route('/prepare/{id}', name: 'edtPrepare', methods: ['GET', 'POST'])]
    public function prepare(Request $request, Sejour $sejour, EntityManagerInterface $entityManager): Response
    {
        
        $date_range = new \DatePeriod($sejour->getDateDebut(), new \DateInterval('P1D'), $sejour->getDateFin());

        $edts = new CollectionEDT();
        foreach ($date_range as $date) {
            $edt = new EDT();
            $edt->setDate($date);
            $edt->setIdMedecin($sejour->getIdMedecin());
            $edt->setIdSejour($sejour);
            $edts->getCollection()->add($edt);
        }
        
        $edt = new EDT();
        $edt->setIdMedecin($sejour->getIdMedecin());
        $edt->setIdSejour($sejour);
        
        $form = $this->createForm(CollectionEDTType::class, $edts);
        $form->handleRequest($request);

        
            $errors = array();
        if ($form->isSubmitted() && $form->isValid()) {
            
            
            $collection = $form->getData();
            $datas = $collection->getCollection();
            
            $edtRepository = $entityManager->getRepository(EDT::class);
            
            foreach($datas as $data) {
                $existing_edts = $edtRepository->findAllByDate($data->getDate(), $data->getIdMedecin());
                if(count($existing_edts) > 5) {
                    array_push($errors,$data->getDate()->format('d-m-Y') . ' : Un médecin ne peut pas avoir plus de 5 patients par jour');
                }
            }
            
            
            
            if(!$errors) {
              foreach($datas as $data) {
                    $entityManager->persist($data);
                    $entityManager->flush();
                }
                
                return $this->redirectToRoute('adminDashboard', ['id' => $sejour->getIdMedecin()->getId()], Response::HTTP_SEE_OTHER);
            }    
        }
        
        return $this->render('edt/prepare.html.twig', [
            'errors' => $errors,
            'form' => $form,
        ]);
    }
    
    #[Route('/', name: 'app_e_d_t_index', methods: ['GET'])]
    public function index(EDTRepository $eDTRepository): Response
    {
        return $this->render('edt/index.html.twig', [
            'e_d_ts' => $eDTRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_e_d_t_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $eDT = new EDT();
        $form = $this->createForm(EDTType::class, $eDT);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($eDT);
            $entityManager->flush();

            return $this->redirectToRoute('app_e_d_t_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('edt/new.html.twig', [
            'e_d_t' => $eDT,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_e_d_t_show', methods: ['GET'])]
    public function show(EDT $eDT): Response
    {
        return $this->render('edt/show.html.twig', [
            'e_d_t' => $eDT,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_e_d_t_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EDT $eDT, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EDTType::class, $eDT);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_e_d_t_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('edt/edit.html.twig', [
            'e_d_t' => $eDT,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_e_d_t_delete', methods: ['POST'])]
    public function delete(Request $request, EDT $eDT, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$eDT->getId(), $request->request->get('_token'))) {
            $entityManager->remove($eDT);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_e_d_t_index', [], Response::HTTP_SEE_OTHER);
    }
}
