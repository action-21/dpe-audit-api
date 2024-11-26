<?php

namespace App\Domain\Eclairage\Service;

use App\Domain\Eclairage\Eclairage;
use App\Domain\Eclairage\Data\NhjRepository;
use App\Domain\Common\Enum\{Energie, Mois, ScenarioUsage, Usage, ZoneClimatique};
use App\Domain\Common\ValueObject\ConsommationCollection;

final class MoteurConsommation
{
    // Constante de puissance d'éclairage en W/m²
    public final const PUISSANCE_ECLAIRAGE = 1.4;
    // Constante de taux d'utilisation de l'éclairage
    public final const TAUX_UTILISATION = 0.9;

    public function __construct(private NhjRepository $njecl_repository) {}

    public function calcule_consommations(Eclairage $entity): ConsommationCollection
    {
        return ConsommationCollection::create(
            usage: Usage::ECLAIRAGE,
            energie: Energie::ELECTRICITE,
            callback: fn(ScenarioUsage $scenario): float => $this->cecl(zone_climatique: $entity->zone_climatique()),
        );
    }

    /**
     * Consommation d'éclairage en kWh
     */
    public function cecl(ZoneClimatique $zone_climatique): float
    {
        $collection = $this->njecl_repository->search_by(zone_climatique: $zone_climatique);
        if (false === $collection->est_valide())
            throw new \DomainException('Valeur forfaitaire Nhj non trouvée');

        return Mois::reduce(fn(float $carry, Mois $mois): float => $carry += (self::TAUX_UTILISATION * self::PUISSANCE_ECLAIRAGE * $collection->nhj(mois: $mois) * $mois->nj()) / 1000);
    }
}
