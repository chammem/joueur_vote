<?php

namespace App\Controller;
use App\Entity\Vote;
use App\Entity\Joueur;
use App\Form\JoueurType;
use App\Form\VoteType;
use App\Repository\JoueurRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JoueurController extends AbstractController
{
    #[Route('/joueur', name: 'app_joueur')]
    public function index(): Response
    {
        return $this->render('joueur/index.html.twig', [
            'controller_name' => 'JoueurController',
        ]);
    }
    #[Route('/showJoueur', name: 'showJoueur')]
    public function showJoueur(JoueurRepository $joueurRepository): Response
    { 
        $joueur=$joueurRepository->showJoueurs();
        
        return $this->render('joueur/ShowJoueur.html.twig', [
            'joueur' => $joueur,
        ]);
    }
    #[Route('/addJoueur', name: 'addJoueur')]
    public function addJoueur(ManagerRegistry $managerRegistry, Request $req): Response
    {   $em=$managerRegistry->getManager();
        $joueur=new Joueur;
        $form=$this->createForm(JoueurType::class,$joueur);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($joueur);
            $em->flush();
            return $this->redirectToRoute('showJoueur');
            
        }
        
        return $this->renderForm('joueur/addJoueur.html.twig', [
            'form' => $form,
        ]);
    }
   
    #[Route('/editJoueur/{id}', name: 'editJoueur')]
    public function editJoueur($id, JoueurRepository $joueurRepository ,ManagerRegistry $managerRegistry, Request $req): Response
    {   $em=$managerRegistry->getManager();
        $dataid=$joueurRepository->find($id);
        $form=$this->createForm(JoueurType::class,$dataid);
        $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('showJoueur');
            
        }
        
        return $this->renderForm('joueur/editJoueur.html.twig', [
            'form' => $form,
        ]);
    }
    
    #[Route('/deleteJoueur/{id}', name: 'deleteJoueur')]
    public function deleteJoueur($id, JoueurRepository $joueurRepository ,ManagerRegistry $managerRegistry, Request $req): Response
    {   $em=$managerRegistry->getManager();
        $dataid=$joueurRepository->find($id);
    
            $em->remove($dataid);
            $em->flush();
            return $this->redirectToRoute('showJoueur');
            
        
     
    }
}
