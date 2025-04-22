<?php

namespace App\Domain\Ecs\Engine\Perte;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\ScenarioClimatique;
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\{Mois, ScenarioUsage, TypePerte, Usage};
use App\Domain\Common\ValueObject\Pertes;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Enum\TypeGenerateur;
use App\Domain\Ecs\Service\EcsTableValeurRepository;

final class PerteStockageIntegre extends EngineRule
{
    private Audit $audit;
    private Generateur $generateur;

    public function __construct(private readonly EcsTableValeurRepository $table_repository,) {}

    /**
     * @see \App\Domain\Audit\Engine\ScenarioClimatique::sollicitations_exterieures()
     */
    public function nref(ScenarioUsage $scenario, Mois $mois): float
    {
        return $this->audit->data()->sollicitations_exterieures->nref(scenario: $scenario, mois: $mois);
    }

    /**
     * Pertes mensuelles de stockage exprimées en Wh
     */
    public function pertes_stockage(): float
    {
        return $this->get("pertes_stockage", function () {
            $vs = $this->generateur->signaletique()->volume_stockage;

            if (0 === $vs) {
                return 0;
            }
            if (false === \in_array($this->generateur->type(), [
                TypeGenerateur::CHAUFFE_EAU_HORIZONTAL,
                TypeGenerateur::CHAUFFE_EAU_VERTICAL,
            ])) {
                return (67662 * \pow($vs, 0.55)) / 12;
            }
            if (null === $cr = $this->table_repository->cr(
                type_generateur: $this->generateur->type(),
                label_generateur: $this->generateur->signaletique()->label,
                volume_stockage: $vs,
            )) {
                throw new \DomainException("Valeur forfaitaire Cr non trouvée");
            }
            return (8592 * (45 / 24) * $vs * $cr) / 12;
        });
    }

    /**
     * Pertes mensuelles de stockage récupérables exprimées en Wh
     */
    public function pertes_stockage_recuperables(ScenarioUsage $scenario, Mois $mois): float
    {
        if (!$this->generateur->position()->position_volume_chauffe) {
            return 0;
        }
        $nref = $this->nref(scenario: $scenario, mois: $mois);
        return 0.48 * $nref * ($this->pertes_stockage() / 8760);
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->ecs()->generateurs() as $generateur) {
            $this->generateur = $generateur;

            $pertes = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::STOCKAGE,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_stockage(),
            );
            $pertes_recuperables = Pertes::create(
                usage: Usage::ECS,
                type: TypePerte::STOCKAGE,
                callback: fn(ScenarioUsage $scenario, Mois $mois) => $this->pertes_stockage_recuperables(
                    scenario: $scenario,
                    mois: $mois,
                ),
            );

            $generateur->calcule($generateur->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
            $generateur->ecs()->calcule($generateur->ecs()->data()->with(
                pertes: $pertes,
                pertes_recuperables: $pertes_recuperables,
            ));
        }
    }

    public static function dependencies(): array
    {
        return [ScenarioClimatique::class];
    }
}
