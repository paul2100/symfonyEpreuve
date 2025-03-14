<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Employe;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Produit;
use App\Form\ConnexionType;
use App\Form\FormationType;

final class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(): Response
    {
        return $this->render('employe/index.html.twig', [
            'controller_name' => 'EmployeController',
        ]);
    }


    #[Route('/ap_admin' , name:'app_Dashboard_admin')]
    public function goToAdmin(Session $session , ManagerRegistry $doctrine , Request $request){
        
        $employe = $session -> get('employe');
    

        if ($employe) { 
            
            $allInscription = $doctrine -> getRepository(Inscription::class) -> findAll(); 

            $formFormation = $this->createForm(FormationType::class);
            $formFormation -> handleRequest($request);

            if ($formFormation -> isSubmitted() && $formFormation -> isValid()) {
                $formationData = $formFormation -> getData();
                $doctrine -> getManager() -> persist($formationData);
                $doctrine -> getManager() -> flush();
                return $this-> render('employe/dashboardAdmin.html.twig' , [
                    'formFormation' => $formFormation -> createView() , 
                    'allInscription' => $allInscription
                ]);
            }
            else {
                return $this-> render('employe/dashboardAdmin.html.twig' , [
                    'formFormation' => $formFormation -> createView()  ,
                    'allInscription' => $allInscription
                ]);
            }
        }
        else {
            return $this-> redirectToRoute('app_connexion');
        }
    

    }






    #[Route('/ap_employe' , name:'app_Dashboard_employe')]
    public function goToEmploye(Session $session , ManagerRegistry $doctrine , Request $request){

        $employe = $session -> get('employe');
        if($session -> get('succes')!= null) {
            $succes = $session -> get('succes');
        } else {
            $succes = null;
            $session -> set('succes' , $succes);
        }



        if ($employe) {
            $formations = $doctrine ->getRepository(Formation::class) -> findAll();
            //$inscription = $doctrine ->getRepository(Inscription::class) -> findBy(['employe' => $employe, 'formations' => $formations]);
            return $this-> render('employe/dashboardEmploye.html.twig' , [
                'formations' => $formations , 
                'succes' => $succes
            ]);
        }
        else {

        return $this-> redirectToRoute('app_connexion');

        }

    }

}
