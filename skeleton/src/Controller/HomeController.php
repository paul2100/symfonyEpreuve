<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Employe;
use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\Produit;
use App\Form\ConnexionType;


final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_connexion');
    }

    #[Route('/connexion' , name:'app_connexion')]
    public function connexion(Session $session , ManagerRegistry $doctrine , Request $request)
    {
        $formConnexion = $this->createForm(ConnexionType::class);
        $formConnexion -> handleRequest($request);

        if ($formConnexion -> isSubmitted() && $formConnexion -> isValid())
        {
            $employeData = $formConnexion -> getData();
            $employe = $doctrine -> getRepository(Employe::class) ->findOneBy(['login' => $employeData -> getLogin() , 'mdp' => $employeData -> getmdp()]);
            if ($employe ) {
                $session -> set('employe' , $employe);
                if($employe -> getStatut() == 0) {
                    return $this->redirectToRoute('app_Dashboard_employe');
                }
                else {
                    return $this->redirectToRoute('app_Dashboard_admin');
                }

            }

            else {
                return $this->render('home/connexion.html.twig', [
                    'formConnexion' => $formConnexion->createView(),
                    'error' => 'Login ou mdp incorrect'
                ], new Response(null, 422)); // Ajouter ce statut
            }

        }
        else {
            return $this->render('home/connexion.html.twig' , [
                'formConnexion' => $formConnexion -> createView(), 
                'error' => null
            ]);
        }
    }
}

