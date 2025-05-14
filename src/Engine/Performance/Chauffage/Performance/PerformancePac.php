<?php

namespace App\Engine\Performance\Chauffage\Performance;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Emetteur;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\TypeEmission;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\ValueObject\Annee;
use App\Engine\Performance\Rule;
use App\Engine\Performance\Scenario\ScenarioClimatique;

final class PerformancePac extends Rule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Engine\Performance\Scenario\ScenarioClimatique::zone_climatique()
     */
    public function zone_climatique(): ZoneClimatique
    {
        return $this->audit->data()->zone_climatique;
    }

    /**
     * Année d'installation
     */
    public function annee_installation(): Annee
    {
        return $this->generateur->annee_installation() ?? $this->audit->batiment()->annee_construction;
    }

    /**
     * Coefficient de performance énergétique
     */
    public function scop(): float
    {
        return $this->get("scop", function () {
            if ($this->generateur->signaletique()->scop) {
                return $this->generateur->signaletique()->scop;
            }

            $scops = [];
            $emissions = $this->generateur->emetteurs()->count()
                ? $this->generateur->emetteurs()->map(fn(Emetteur $item) => $item->type_emission())->toArray()
                : [TypeEmission::from_type_generateur($this->generateur->type())];

            foreach ($emissions as $emission) {
                if (null === $scop = $this->table_repository->scop(
                    zone_climatique: $this->zone_climatique(),
                    type_generateur: $this->generateur->type(),
                    annee_installation_generateur: $this->annee_installation(),
                    type_emission: $emission,
                )) {
                    throw new \DomainException("Valeurs forfaitaires SCOP non trouvées");
                }
                $scops[] = $scop;
            }
            return max($scops);
        });
    }

    public static function match(Generateur $generateur): bool
    {
        return $generateur->type()->is_pac() && false === $generateur->position()->generateur_multi_batiment;
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->generateurs() as $generateur) {
            if (false === self::match($generateur)) {
                continue;
            }
            $this->generateur = $generateur;
            $this->clear();

            $generateur->calcule($generateur->data()->with(
                scop: $this->scop(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ScenarioClimatique::class];
    }
}
