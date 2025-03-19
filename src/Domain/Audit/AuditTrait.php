<?php

namespace App\Domain\Audit;

use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Common\Enum\ZoneClimatique;

trait AuditTrait
{
    abstract public function audit(): Audit;

    public function type_batiment(): TypeBatiment
    {
        return $this->audit()->batiment->type;
    }

    public function annee_construction_batiment(): int
    {
        return $this->audit()->batiment->annee_construction;
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit()->adresse->zone_climatique;
    }

    public function surface_habitable_reference(): float
    {
        return $this->audit()->logement?->surface_habitable ?? $this->audit()->batiment->surface_habitable;
    }

    public function hauteur_sous_plafond_reference(): float
    {
        return $this->audit()->logement?->hauteur_sous_plafond ?? $this->audit()->batiment->hauteur_sous_plafond;
    }

    public function surface_habitable_moyenne(): float
    {
        return $this->audit()->batiment->surface_habitable / $this->audit()->batiment->logements;
    }
}
