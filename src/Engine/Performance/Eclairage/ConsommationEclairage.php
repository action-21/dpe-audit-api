<?php

namespace App\Engine\Performance\Eclairage;

use App\Domain\Audit\Audit;
use App\Domain\Common\Enum\{Energie, ScenarioUsage, Usage, ZoneClimatique};
use App\Domain\Common\ValueObject\Consommations;
use App\Domain\Eclairage\Service\EclairageTableValeurRepository;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\{ScenarioClimatique, ZoneThermique};

final class ConsommationEclairage extends Rule
{
    // Constante de puissance d'éclairage en W/m²
    public final const PUISSANCE_ECLAIRAGE = 1.4;
    // Constante de taux d'utilisation de l'éclairage
    public final const TAUX_UTILISATION = 0.9;

    private Audit $audit;

    public function __construct(private readonly EclairageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Engine\Performance\Scenario\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

    /**
     * Consommation d'éclairage en kWh/m²
     */
    public function cecl(): float
    {
        return $this->nhecl() * self::PUISSANCE_ECLAIRAGE * $this->surface_habitable() * self::TAUX_UTILISATION / 1000;
    }

    /**
     * Nombre d'heures d'éclairage par an
     */
    public function nhecl(): float
    {
        return $this->get("nhecl", function () {
            if (null === $value = $this->table_repository->nhecl(
                zone_climatique: $this->zone_climatique(),
            )) {
                throw new \DomainException('Valeur forfaitaires "nhecl" non trouvée');
            }
            return $value;
        });
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;
        $this->clear();

        $consommations = Consommations::create(
            usage: Usage::ECLAIRAGE,
            energie: Energie::ELECTRICITE,
            callback: fn(ScenarioUsage $scenario) => $this->cecl(),
        );
        $entity->eclairage()->calcule($entity->eclairage()->data()->with(
            consommations: $consommations,
        ));
        $entity->calcule($entity->data()->with(
            consommations: $consommations,
        ));
    }

    public static function dependencies(): array
    {
        return [ZoneThermique::class, ScenarioClimatique::class];
    }
}
