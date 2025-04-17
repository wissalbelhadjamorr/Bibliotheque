<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    public function findByFilters(?string $titre, ?string $auteur): array
{
    $queryBuilder = $this->createQueryBuilder('l')
        ->leftJoin('l.auteur', 'a');  // Joindre l'entité Auteur

    // Filtrer par titre
    if ($titre) {
        $queryBuilder->andWhere('l.titre LIKE :titre')
                     ->setParameter('titre', '%' . $titre . '%');
    }

    // Filtrer par auteur seulement si la variable $auteur n'est pas vide
    if ($auteur) {
        $queryBuilder->andWhere('a.nom LIKE :auteur')
                     ->setParameter('auteur', '%' . $auteur . '%');
    }

    return $queryBuilder->getQuery()->getResult();
}

}

   