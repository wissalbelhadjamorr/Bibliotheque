<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livre')]
final class LivreController extends AbstractController
{
    #[Route(name: 'app_livre_index', methods: ['GET'])]
    public function index(
        Request $request,
        LivreRepository $livreRepository,
        PaginatorInterface $paginator
    ): Response {
        $titre = $request->query->get('titre');
        $auteur = $request->query->get('auteur');

        $query = $livreRepository->createQueryBuilder('l')
            ->leftJoin('l.auteur', 'a')
            ->addSelect('a')
            ->leftJoin('l.genre', 'g')
            ->addSelect('g');

        if ($titre) {
            $query->andWhere('l.titre LIKE :titre')
                  ->setParameter('titre', '%' . $titre . '%');
        }

        if ($auteur) {
            $query->andWhere('a.nom LIKE :auteur')
                  ->setParameter('auteur', '%' . $auteur . '%');
        }

        // Pagination
        $livres = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // La page courante
            5 // Nombre d'éléments par page
        );

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
        ]);
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
        if ($this->isCsrfTokenValid('delete' . $livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/emprunter', name: 'app_emprunt_livre')]
    public function emprunter(Livre $livre, EntityManagerInterface $em): Response
    {
        $this->addFlash('success', 'Livre emprunté avec succès !');
        return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
    }

    #[Route('/livre/{id}', name: 'app_livre_detail')]
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
