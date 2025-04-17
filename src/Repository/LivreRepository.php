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

// src/Repository/LivreRepository.php

public function findByFilters(?string $titre, ?string $auteur): array
{
    $queryBuilder = $this->createQueryBuilder('l');

    // Filtrer par titre (si le titre est défini)
    if ($titre) {
        $queryBuilder->andWhere('l.titre LIKE :titre')
                     ->setParameter('titre', '%' . $titre . '%');
    }

    // Filtrer par auteur (si l'auteur est défini)
    if ($auteur) {
        $queryBuilder->join('l.auteur', 'a')  // Jointure avec l'entité Auteur
                     ->andWhere('a.nom LIKE :auteur')
                     ->setParameter('auteur', '%' . $auteur . '%');
    }

    // Récupérer les résultats
    return $queryBuilder->getQuery()->getResult();
}

}

    
    //    /**
    //     * @return Livre[] Returns an array of Livre objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Livre
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

