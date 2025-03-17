<?php

namespace App\Controller;

use App\Entity\Formation;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;


final class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(): Response
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }

    #[Route('/afficherAllFormation' , name:'app_afficher_all_formation')]
    public function afficherAllFormation(Session $session , ManagerRegistry $doctrine , Request $request){

        $employe = $session -> get('employe');

        if($employe) {
            $allformations = $doctrine -> getRepository(Formation::class) -> findAll();

            if(!$allformations) {
                return $this-> redirectToRoute('app_Dashboard_admin');
            }

            return $this->render('formation/allFormation.html.twig' , [
                'allformation' => $allformations
            ]);

        }
        else {
            return $this-> redirectToRoute('app_connexion');
        }
    }


    #[Route('/deletFormation/{id}' , name:'app_deleteFormation')]
    public function supprimerUneFormation (Session $session , ManagerRegistry $doctrine , Request $request , $id){

        $employe = $session -> get('employe');


        if ($employe) {
            
            $formation = $doctrine -> getRepository(Formation::class) -> findOneBy(['id' => $id]);

            if ($formation != null) {
                $doctrine -> getManager() -> remove($formation);
                $doctrine -> getManager() -> flush();
                $session -> set('succes' , 'Formation supprimer avec succes !');
            }    
            
            else {
                $session -> set('succes' , 'Aucune formations touvées');
                return $this-> redirectToRoute('app_afficher_all_formation');
            }
        
        }
        
        return $this-> redirectToRoute('app_afficher_all_formation');
    }
}
