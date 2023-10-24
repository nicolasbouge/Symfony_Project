<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ListOfCardsController extends AbstractController
{
    #[Route('/list/of/cards', name: 'list_of_cards')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $client = HttpClient::create();
        $response = $client->request('GET', 'https://db.ygoprodeck.com/api/v7/cardinfo.php');

        $data = $response->toArray(); // Convertit la réponse JSON en tableau

        $pagination = $paginator->paginate(
            $data['data'], // Données à paginer
            $request->query->getInt('page', 1), // Récupère le numéro de la page depuis la requête
            100 // Nombre d'éléments par page (100 cartes par page)
        );

        return $this->render('list_of_cards/index.html.twig', [
            'controller_name' => 'ListOfCardsController',
            'pagination' => $pagination,
        ]);
    }       
}
