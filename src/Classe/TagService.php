<?php

namespace App\Classe;

use App\Repository\TagRepository;

class TagService
{
    private $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Retourne un tableau des tags pour lesquels il existe des événements
     */
    public function tagWithEvent(): array
    {
        // Récupère les tags qui ont des événements
        $tags = [];
        $allTags = $this->tagRepository->findBy([], ['name' => 'ASC']);

        // Pour chaque $tag on récupère les événements
        foreach ($allTags as $key => $tag) {
            // Si il y a des événements et que le tag n'est pas dans le tableau
            if (count($tag->getEvents()) > 0 && !array_key_exists($key, $tags)) {
                // Pour chaque événement on vérifie que la date est supérieure à aujourd'hui
                foreach ($tag->getEvents() as $event) {
                    if ($event->getDateAt() > new \DateTime('today')) {
                        $tags[$key] = $tag;
                    }
                }
            }
        }

        return $tags;
    }
}
