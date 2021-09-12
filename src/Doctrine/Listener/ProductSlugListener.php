<?php

namespace App\Doctrine\Listener;

use App\Entity\Product;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductSlugListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    // la méthode du listener reçoit l'évent en argument qui donne accès
    // à l'entity manager et l'entité liée à l'évènement
    public function prePersist(Product $entity, LifecycleEventArgs $event)
    {
        // $entity = $event->getObject();

        // // si on veut que la méthode ne s'applique qu'à certaines entités
        // if (!$entity instanceof Product) {
        //     return;
        // }

        if (empty($entity->getSlug())) {
            $entity->setSlug(strtolower($this->slugger->slug($entity->getName())));
        }
    }
}
