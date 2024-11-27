<?php

namespace App\Domain\Production\Service;

use App\Domain\Common\Enum\Mois;
use App\Domain\Production\Data\KpvRepository;
use App\Domain\Production\Entity\PanneauPhotovoltaique;
use App\Domain\Production\ValueObject\{ProductionPhotovoltaique, ProductionPhotovoltaiqueCollection};
use App\Domain\Simulation\Simulation;

final class MoteurProduction
{
    public final const SURFACE_CAPTEUR_UNITAIRE = 1.6;
    public final const RENDEMENT_MODULE = 0.17;
    public final const COEFFICIENT_PERTE = 0.86;

    public function __construct(private KpvRepository $kpv_repository) {}

    public function calcule_production_photovoltaique(PanneauPhotovoltaique $entity, Simulation $simulation): ProductionPhotovoltaiqueCollection
    {
        $collection = [];
        $surface_capteurs = $this->surface_capteurs(
            nombre_modules: $entity->modules(),
            surface_capteurs: $entity->surface_capteurs()
        );
        $kpv = $this->kpt(
            orientation: $entity->orientation(),
            inclinaison: $entity->inclinaison()
        );

        foreach (Mois::cases() as $mois) {
            $ppv = $this->ppv(
                surface_habitable: $simulation->surface_habitable_reference(),
                kpv: $kpv,
                surface_capteurs: $surface_capteurs,
                epv: $simulation->audit()->situation()->epv(mois: $mois),
            );
            $collection[] = ProductionPhotovoltaique::create(mois: $mois, ppv: $ppv);
        }
        return new ProductionPhotovoltaiqueCollection($collection);
    }

    public function ppv(float $surface_habitable, float $kpv, float $surface_capteurs, float $epv): float
    {
        return ($kpv * $surface_capteurs * self::RENDEMENT_MODULE * $epv * self::COEFFICIENT_PERTE) / $surface_habitable;
    }

    public function surface_capteurs(int $nombre_modules, ?float $surface_capteurs): float
    {
        return $surface_capteurs ?? $nombre_modules * self::SURFACE_CAPTEUR_UNITAIRE;
    }

    public function kpt(float $orientation, float $inclinaison): float
    {
        if (null === $value = $this->kpv_repository->find_by(
            orientation: $orientation,
            inclinaison: $inclinaison,
        )) {
            dd($orientation, $inclinaison);
            throw new \DomainException("Valeur forfaitaire kpv non trouvée");
        }

        return $value->kpv;
    }
}
