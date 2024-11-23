<?php

namespace App\Domain\Eclairage;

use App\Domain\Eclairage\Service\MoteurConsommation;

final class EclairageService
{
    public function __construct(private MoteurConsommation $moteur_consommation) {}

    public function calcule(Eclairage $entity): Eclairage
    {
        $entity->calcule_consommations($this->moteur_consommation);
        return $entity;
    }
}
