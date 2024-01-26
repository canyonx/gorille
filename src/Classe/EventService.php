<?php

namespace App\Classe;

use App\Entity\Tag;

class EventService
{

    /**
     * Retourne un tableau des événements qui sont associés au tag $categorie
     */
    public function eventsWithTag(Tag $categorie)
    {
        // Récupère les événements a venir qui ont le tag $categorie
        $futurEvents = [];
        $events = $categorie->getEvents();

        foreach ($events as $key => $event) {
            if ($event->getDateAt() >= new \DateTime('today')) {
                $futurEvents[$key] = $event;
            }
        }

        // Classe les événements de $futursEvents par date
        usort($futurEvents, function ($a, $b) {
            return date_timestamp_get($a->getdateAt()) > date_timestamp_get($b->getDateAt());
        });

        return $futurEvents;
    }
}
