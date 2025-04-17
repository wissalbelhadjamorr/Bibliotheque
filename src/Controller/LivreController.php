<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livre')]
final class LivreController extends AbstractController
{
    #[Route(name: 'app_livre_index', methods: ['GET'])]
    public function index(Request $request, LivreRepository $livreRepository): Response
{
    // Récupérer les valeurs des paramètres de recherche
    $titre = $request->query->get('titre');
    $auteur = $request->query->get('auteur');
    
    // Appliquer la méthode de recherche avec titre et auteur
    $livres = $livreRepository->findByFilters($titre, $auteur);
    
    return $this->render('livre/index.html.twig', [
        'livres' => $livres,
    ]);
}

#[Route('/{id}/emprunter', name: 'app_emprunt_livre')]
public function emprunter(Livre $livre, EntityManagerInterface $em): Response
{
    // logique d'emprunt ici...

    $this->addFlash('success', 'Livre emprunté avec succès !');
    return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
}

    

    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
    public function findByFilters(?string $titre, ?string $auteur): array
{
    $queryBuilder = $this->createQueryBuilder('l');

    if ($titre) {
        $queryBuilder->andWhere('l.titre LIKE :titre')
                     ->setParameter('titre', '%' . $titre . '%');
    }

    if ($auteur) {
        $queryBuilder->andWhere('l.auteur LIKE :auteur')
                     ->setParameter('auteur', '%' . $auteur . '%');
    }

    return $queryBuilder->getQuery()->getResult();
}

    /**
     * @Route("/livre/{id}", name="app_livre_detail")
     */
    public function detail(Livre $livre): Response
    {
        if (!$livre) {
            throw $this->createNotFoundException('Le livre n\'a pas été trouvé.');
        }

        return $this->render('livre/detail.html.twig', [
            'livre' => $livre,
        ]);
    }
}
