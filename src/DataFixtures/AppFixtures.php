<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use App\Repository\UserRepository;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private $slugger;
    private $encoder;
    private $userRepository;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
        $this->userRepository = $userRepository;
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

        $products = [];

        for ($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category->setName($faker->department);
            // on set le slug via un event orm.doctrine.entity_listener
            // ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);

            for ($i = 0; $i < 25; $i++) {
                $product = new Product();
                $product->setName($faker->productName())
                    ->setPrice($faker->price(200, 45000))
                    // ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setCategory($category)
                    ->setShortDescription($faker->sentence(20))
                    ->setMainPicture($faker->imageUrl(400, 400, true));
                $manager->persist($product);

                $products[] = $product;
            };
        }

        $admin = new User;
        $hash = $this->encoder->encodePassword($admin, "password");
        $admin->setEmail("admin@gmail.com")
            ->setFullName("Admin")
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $users = [];

        for ($u = 0; $u < 5; $u++) {
            $user = new User;
            $hash = $this->encoder->encodePassword($admin, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($hash);

            $users[] = $user;

            $manager->persist($user);
        }

        for ($p = 0; $p < mt_rand(15, 20); $p++) {
            $purchase = new Purchase;

            $purchase->setFullName($faker->name())
                ->setAddress($faker->streetAddress())
                ->setPostalCode($faker->postcode())
                ->setCity($faker->city())
                ->setTotal(mt_rand(200, 45299))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months', 'now'));

            if ($faker->boolean(75)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }
            $purchase->setUSer($faker->randomElement($users));
            $selectedProducts = $faker->randomElements($products, rand(3, 5));

            foreach ($selectedProducts as $product) {
                $purchaseItem = new PurchaseItem();

                $purchaseItem->setProduct($product)
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setPurchase($purchase)
                    ->setQuantity(rand(1, 4))
                    ->setTotal($product->getPrice() * $purchaseItem->getQuantity());

                $manager->persist($purchaseItem);
            }

            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
