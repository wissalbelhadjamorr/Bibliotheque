<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AuteurRepository;
use App\Repository\LivreRepository;
use App\Repository\GenreRepository;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]

    
    public function index(LivreRepository $livreRepo, AuteurRepository $auteurRepo, GenreRepository $genreRepo): Response
    {
        $livres = $livreRepo->findBy([], ['id' => 'DESC'], 5); // 5 derniers livres
        $auteurs = $auteurRepo->findAll();
        $genres = $genreRepo->findAll();
    
        return $this->render('home/index.html.twig', [
            'livres' => $livres,
            'auteurs' => $auteurs,
            'genres' => $genres,
        ]);
    }
    
}
