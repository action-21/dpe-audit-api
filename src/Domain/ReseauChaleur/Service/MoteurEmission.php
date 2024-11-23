<?php

namespace App\Domain\ReseauChaleur\Service;

use App\Domain\Common\Type\Id;
use App\Domain\ReseauChaleur\ReseauChaleurRepository;

final class MoteurEmission
{
    public function __construct(private ReseauChaleurRepository $repository) {}

    public function emissions_reseau_chaleur(?Id $id, float $consommation_ef): float
    {
        if (null === $id)
            return $consommation_ef * 0.385;
    }

    public function emissions_reseau_froid(?Id $id, float $consommation_ef): float
    {
        if (null === $id)
            return $consommation_ef * 0.120;
    }
}
