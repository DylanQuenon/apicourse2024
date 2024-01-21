<?php

namespace App\Events;

use App\Entity\User;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordEncoderSubscriber implements EventSubscriberInterface{

    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
 
    public static function getSubscribedEvents(){
        return [
            KernelEvents::VIEW => ['encodePassword', EventPriorities::PRE_WRITE] 
            //on utilise le kernelEvents pour écouter les requêtes HTTP
            //on utilise la méthode encodePassword pour encoder le mot de passe
            //on utilise le pré-write pour éviter que l'encodePassword soit exécuté avant l'enregistrement du user
        ];
    }

    public function encodePassword(ViewEvent $event){
        $user = $event->getControllerResult();// récupère le user déserialize
        //on récupère le user qui vient de l'event


        $method= $event->getRequest()->getMethod(); // récup la méthode (GET, POST, ...)

        /* Vérifier quand la requête envoie un user et qu'elle est de type POST */
        if($user instanceof User && $method==="POST"){
            $hash = $this->encoder->hashPassword($user, $user->getPassword()); //on récup et on hash le mot de passe
            $user->setPassword($hash);
        }
    }
}