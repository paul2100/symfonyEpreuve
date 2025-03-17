<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Inscription;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Employe;


final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(): Response
    {
        return $this->render('inscription/index.html.twig', [
            'controller_name' => 'InscriptionController',
        ]);
    }

    #[Route('/addinscription/{id}' , name: 'app_add_inscription')]
    public function ajouterInscription(Session $session , ManagerRegistry $doctrine , Request $request , $id){

        $employe = $session -> get("employe");


        if ($employe) {
            $formation = $doctrine -> getRepository(Formation::class) -> findOneBy(['id' => $id]);
            if ($formation){
                $inscriptions = $doctrine -> getRepository(Inscription::class) -> findOneBy(['formation' => $formation, 'employe' => $employe]);

                if(!$inscriptions) {
                    $inscription = new Inscription();
                    $inscription -> setEmploye($doctrine -> getRepository(Employe::class) -> findOneBy(['id' => $employe -> getId()]));
                    $inscription -> setStatut("En Attente");
                    $inscription -> setFormation($formation);
                    $doctrine -> getManager() -> persist($inscription);
                    $doctrine -> getManager() -> flush();
                    $session -> set('succes' , 'formation ajouter');
                    return $this->redirectToRoute('app_Dashboard_employe');
                }
                else {
                    $session -> set('succes' , 'formation deja ajouter');
                    return $this->redirectToRoute('app_Dashboard_employe');

                }
            }
            else {
                return $this-> redirectToRoute('app_Dashboard_employe');
            }
        }
        else {
            return $this-> redirectToRoute('app_connexion');
        }
    }


    #[Route('/gestionInscription/{statut}/{inscriptionId}', name : 'app_gestionInscription')] 
        public function gestionInscription(Session $session , ManagerRegistry $doctrine , Request $request , $statut , $inscriptionId){

            $employe = $session -> get('employe');

            if (!$employe) {
                return $this-> redirectToRoute('app_connexion');
            }
            $inscription = $doctrine -> getRepository(Inscription::class) -> findOneBy(['id' => $inscriptionId]);
            if (!$inscription) {
                return $this-> redirectToRoute('app_Dashboard_admin');
            }
            if ($statut == 0) {
                $inscription -> setStatut('Refuser');
            }
            else {
                $inscription -> setStatut('Accepter');
            }
            $doctrine -> getManager() -> persist($inscription);
            $doctrine -> getManager() -> flush();
            return $this-> redirectToRoute('app_Dashboard_admin');
    }
}