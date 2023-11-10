<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Entity\Vote;
use App\Form\VoteType;
use App\Repository\JoueurRepository;
use App\Repository\VoteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoteController extends AbstractController
{
    #[Route('/vote', name: 'app_vote')]
    public function index(): Response
    {
        return $this->render('vote/index.html.twig', [
            'controller_name' => 'VoteController',
        ]);
    }
    
    #[Route('/showVote', name: 'showVote')]
    public function showVote(VoteRepository $voteRepository): Response
    {
        $vote=$voteRepository->findAll();
        return $this->render('vote/showVote.html.twig', [
            'vote' => $vote,
        ]);
    }

    #[Route('/addVote', name: 'addVote')]
    public function addVote(VoteRepository $voteRepository , ManagerRegistry $managerRegistry , Request $req): Response
    {   
        $em=$managerRegistry->getManager();
        $vote=new Vote;
        $form=$this->createForm(VoteType::class,$vote);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid())
        {
            $vote->setDate (new \DateTime());
            $em->persist($vote);
            $em->flush();
            
                $joueur = $vote->getJoueur();
                $joueur->setMoyenneVote(
                $voteRepository->getSommeVotebyJoueur($joueur->getId()) / $joueur->getVotes()->count());
                $em->persist($joueur);
                $em->flush();
            
            return $this->redirectToRoute('showVote');

        }
        return $this->renderForm('vote/addVote.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/detailVote/{id}', name: 'detailVote')]
    public function detailVote($id , JoueurRepository $joueurRepository , VoteRepository $voteRepository): Response
    {   
        
        $votes=$voteRepository->getVoteByJoueur($id);
        $joueur=$joueurRepository->find($id);
        
        return $this->render('vote/detailJoueur.html.twig', [
            'votes' => $votes,
            'joueur' => $joueur,
        ]);
    }

    #[Route('/editVote/{id}', name: 'editVote')]
    public function editVote($id , VoteRepository $voteRepository,ManagerRegistry $managerRegistry , Request $req): Response
    {   
        $em=$managerRegistry->getManager();
        $dataid=$voteRepository->find($id);
        $form=$this->createForm(VoteType::class,$dataid);
        $form->handleRequest($req);

        if($form->isSubmitted() and $form->isValid())
        {
            $em->persist($dataid);
            $em->flush();
            return $this->redirectToRoute('showVote');

        }
        
        return $this->renderForm('vote/editVote.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/deleteVote/{id}', name: 'deleteVote')]
    public function deleteVote($id , VoteRepository $voteRepository,ManagerRegistry $managerRegistry , Request $req): Response
    {   
        $em=$managerRegistry->getManager();
        $dataid=$voteRepository->find($id);
       
            $em->remove($dataid);
            $em->flush();
            return $this->redirectToRoute('showVote');

        
    }

    
}
