<?php

namespace App\Domain\Production\Engine;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\{ScenarioClimatique, ZoneThermique};
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\Mois;
use App\Domain\Production\Entity\PanneauPhotovoltaique;
use App\Domain\Production\Service\ProductionTableValeurRepository;

final class ProductionRenouvelable extends EngineRule
{
    public final const SURFACE_CAPTEUR_UNITAIRE = 1.6;
    public final const RENDEMENT_MODULE = 0.17;
    public final const COEFFICIENT_PERTE = 0.86;

    private Audit $audit;
    private PanneauPhotovoltaique $panneau_photovoltaique;

    public function __construct(private readonly ProductionTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::surface_habitable()
     */
    public function surface_habitable(): float
    {
        return $this->audit->data()->surface_habitable;
    }

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function epv(Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->epv(mois: $mois);
    }

    /**
     * Surface totale des capteurs photovoltaïques exprimée en m²
     */
    public function surface(): float
    {
        return $this->panneau_photovoltaique->surface()
            ?? $this->panneau_photovoltaique->modules() * self::SURFACE_CAPTEUR_UNITAIRE;
    }

    /**
     * Coefficient de pondération prenant en compte l’altération par rapport
     * à l’orientation optimale
     */
    public function kpv(): float
    {
        if (null === $kpv = $this->table_repository->kpv(
            orientation: $this->panneau_photovoltaique->orientation(),
            inclinaison: $this->panneau_photovoltaique->inclinaison(),
        )) {
            throw new \DomainException("Valeur forfaitaire kpv non trouvée");
        }
        return $kpv;
    }

    /**
     * Production photovoltaïque du panneau exprimée en kWh/an
     */
    public function ppv(): float
    {
        $ppv = 0;
        $kpv = $this->kpv();
        $surface = $this->surface();
        $surface_habitable = $this->surface_habitable();

        foreach (Mois::cases() as $mois) {
            $epv = $this->epv(mois: $mois);
            $ppv += $kpv * $surface * self::RENDEMENT_MODULE * $epv * self::COEFFICIENT_PERTE;
        }
        return $ppv / $surface_habitable;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;
        $production = 0;

        foreach ($entity->production()->panneaux_photovoltaiques() as $panneau) {
            $this->panneau_photovoltaique = $panneau;

            $production += $ppv = $this->ppv();

            $panneau->calcule($panneau->data()->with(
                kpv: $this->kpv(),
                surface: $this->surface(),
                production: $ppv,
            ));
        }
        $entity->production()->calcule($entity->production()->data()->with(
            production: $production
        ));
    }

    public static function dependencies(): array
    {
        return [
            ZoneThermique::class,
            ScenarioClimatique::class,
        ];
    }
}
