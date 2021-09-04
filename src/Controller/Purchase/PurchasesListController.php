<?php

declare(strict_types=1);

namespace App\Controller\Purchase;

use App\Entity\PurchaseItem;
use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/purchases", name="purchase_index")
     * @IsGranted("ROLE_USER", message="Vous devez être connecté pour visualiser vos commandes")
     */
    public function index(): Response
    {
        // Vérifier qu'un utilisateur est connecté et qui est connecté
        /** @var User */
        $user = $this->security->getUser();

        // Si ok, récupérer les commandes de l'utilisateur connecté
        $purchases = $user->getPurchases();

        // Passer les commances à twig pour affichage
        return $this->render('purchase/index.html.twig', [
            'purchases' => $purchases
        ]);
    }
}
