<?php
// src/DataFixtures/AppFixtures.php
namespace App\DataFixtures;

use App\Entity\Livre;
use App\Entity\Auteur;
use App\Entity\Genre;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Crée un genre
        $genre = new Genre();
        $genre->setNom('Fiction')
              ->setDescription('Genre de fiction');
        $manager->persist($genre);

        // Crée un auteur
        $auteur = new Auteur();
        $auteur->setNom('J.K. Rowling')
               ->setBiographie('Biographie de l\'auteur.')
               ->setDateDeNaissance(new \DateTime('1965-07-31'));
        $manager->persist($auteur);

        // Crée un livre
        $livre = new Livre();
        $livre->setTitre('Harry Potter et la Pierre Philosophale')
              ->setISBN('9780747532699')
              ->setDisponible(true)
              ->setAuteur($auteur)
              ->setGenre($genre);
        $manager->persist($livre);

        // Enregistre les données
        $manager->flush();
    }
}
