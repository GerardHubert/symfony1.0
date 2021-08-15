<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        // On crée un boucle qui va créer 3 catégories.
        // On fait persister les catégories
        // Pour chaque catégorie on crée une autre boucle afin de créer 25 objet par catégorie.
        // Comme il y a une liaison entre Product et Category avec une clé étrangere sur product, on passe l'objet category nouvellement créé dans setCategory
        // On fait persister les produits.
        // On flush le tout

        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            for ($i = 0; $i < 25; $i++) {
                $product = new Product();
                $product->setName($faker->productName())
                    ->setPrice($faker->price(200, 45000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(200, 200, true));
                $manager->persist($product);
            };
        }

        $manager->flush();
    }
}
