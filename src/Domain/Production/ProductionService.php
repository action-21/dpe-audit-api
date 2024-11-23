<?php

namespace App\Domain\Production;

use App\Domain\Production\Service\MoteurProduction;

/**
 * @see App\Domain\Audit\AuditService::calcule()
 */
final class ProductionService
{
    public function __construct(private MoteurProduction $moteur_production) {}

    public function calcule_production(Production $entity): Production
    {
        $entity->calcule_production($this->moteur_production);
        return $entity;
    }
}
