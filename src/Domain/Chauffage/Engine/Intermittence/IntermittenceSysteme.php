<?php

namespace App\Domain\Chauffage\Engine\Intermittence;

use App\Domain\Audit\Audit;
use App\Domain\Audit\Engine\ZoneThermique;
use App\Domain\Chauffage\Entity\{Generateur, Installation, Systeme};
use App\Domain\Chauffage\Enum\{TypeEmission, TypeIntermittence};
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Chauffage\ValueObject\{Intermittence, Intermittences};
use App\Domain\Common\EngineRule;
use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe;
use App\Domain\Enveloppe\Engine\Inertie\InertieEnveloppe;

final class IntermittenceSysteme extends EngineRule
{
    protected Audit $audit;
    protected Systeme $systeme;

    public function __construct(protected readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * @see \App\Domain\Enveloppe\Engine\Inertie\InertieEnveloppe::inertie()
     */
    public function inertie_lourde(): bool
    {
        return $this->audit->enveloppe()->data()->inertie->est_lourd();
    }

    /**
     * @see \App\Domain\Audit\Engine\ZoneThermique::volume_habitable()
     */
    public function volume_habitable(): float
    {
        return $this->audit->data()->volume_habitable;
    }

    /**
     * @see \App\Domain\Enveloppe\Engine\Deperdition\DeperditionEnveloppe::gv()
     */
    public function gv(): float
    {
        return $this->audit->enveloppe()->data()->deperditions->get();
    }

    public function generateur(): Generateur
    {
        return $this->systeme->generateur();
    }

    public function installation(): Installation
    {
        return $this->systeme->installation();
    }

    /**
     * Type d'intermittence
     */
    private function type_intermittence(): TypeIntermittence
    {
        return TypeIntermittence::determine(
            regulation_centrale: $this->installation()->regulation_centrale(),
            regulation_terminale: $this->installation()->regulation_terminale(),
            chauffage_collectif: $this->generateur()->position()->generateur_collectif,
        );
    }

    /**
     * Coefficient d'intermittence
     */
    public function i0(): float
    {
        return $this->get("i0", function () {
            if ($this->systeme->emetteurs()->count() === 0) {
                if (null === $i0 = $this->table_repository->i0(
                    type_batiment: $this->audit->batiment()->type,
                    type_emission: TypeEmission::from_type_generateur($this->generateur()->type()),
                    type_intermittence: $this->type_intermittence(),
                    regulation_terminale: $this->installation()->regulation_terminale()->presence_regulation,
                    inertie_lourde: $this->inertie_lourde(),
                    comptage_individuel: $this->installation()->comptage_individuel(),
                    chauffage_collectif: $this->generateur()->position()->generateur_collectif,
                    chauffage_central: false,
                )) {
                    throw new \DomainException('Valeur forfaitaire I0 non trouvée');
                }
                return $i0;
            }
            /** @var float[] */
            $i0s = [];

            foreach ($this->systeme->emetteurs() as $emetteur) {
                if (null === $i0 = $this->table_repository->i0(
                    type_batiment: $this->audit->batiment()->type,
                    type_emission: $emetteur->type_emission(),
                    type_intermittence: $this->type_intermittence(),
                    regulation_terminale: $this->installation()->regulation_terminale()->presence_regulation,
                    inertie_lourde: $this->inertie_lourde(),
                    comptage_individuel: $this->installation()->comptage_individuel(),
                    chauffage_collectif: $this->generateur()->position()->generateur_collectif,
                    chauffage_central: true,
                )) {
                    throw new \DomainException('Valeur forfaitaire I0 non trouvée');
                }
                $i0s[] = $i0;
            }
            return array_sum($i0s) / count($i0s);
        });
    }

    /**
     * Facteur d'intermittence
     */
    public function int(): float
    {
        $g = $this->gv() / $this->volume_habitable();
        $i0 = $this->i0();
        return $i0 / (1 + 0.1 * ($g - 1));
    }

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;

        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;
            $this->clear();

            $intermittences = ScenarioUsage::each(fn(ScenarioUsage $scenario) => Intermittence::create(
                scenario: $scenario,
                i0: $this->i0(),
                int: $this->int(),
            ));

            $systeme->calcule($systeme->data()->with(
                intermittences: Intermittences::create(...$intermittences),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            ZoneThermique::class,
            InertieEnveloppe::class,
            DeperditionEnveloppe::class,
        ];
    }
}
