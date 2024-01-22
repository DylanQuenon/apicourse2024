<?php
namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedSubscriber{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        // récup l'utilisateur (pour avoir fistname et le lastname)
        $user = $event->getUser(); //récup l'user
        $data = $event->getData(); //récup un tableau qui contient les données de base sur l'utilisateur dans le jwt
        $data['firstName']=$user->getFirstName();
        $data['lastName']=$user->getLastName();

        $event->setData($data); //on met à jour les données du jwt


    }
}