<?php

namespace WsunBundle\Listener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;

use WsunBundle\Entity\Pedido;

class SearchIndexer {
    
    private $tokenStorage;
            
    public function __construct(TokenStorageInterface $tokenStorage) {        
        $this->tokenStorage = $tokenStorage;
       
    }
       
    public function prePersist(LifecycleEventArgs $args)
    {        
        $user = $this->tokenStorage->getToken()->getUser();        
        $entity = $args->getEntity();
        //var_dump($user);
        if ($entity instanceof Pedido)
        {
            $entity->setIdUsuario($user);
        }                        
    }

    

}
