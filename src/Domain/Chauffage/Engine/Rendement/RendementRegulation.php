<?php

namespace App\Domain\Chauffage\Engine\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TypeEmission;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\EngineRule;

final class RendementRegulation extends EngineRule
{
    protected Systeme $systeme;

    public function __construct(protected readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * Rendement de régulation
     */
    public function rr(): float
    {
        $installation = $this->systeme->installation();
        $generateur = $this->systeme->generateur();

        if ($this->systeme->emetteurs()->count() === 0) {
            if (null === $rr = $this->table_repository->rr(
                type_emission: TypeEmission::from_type_generateur($generateur->type()),
                type_generateur: $generateur->type(),
                label_generateur: $generateur->signaletique()->label,
                reseau_collectif: $generateur->position()->generateur_collectif,
                presence_regulation_terminale: $installation->regulation_terminale()->presence_regulation,
                presence_robinet_thermostatique: null,
            )) {
                throw new \DomainException('Valeur forfaitaire Rr non trouvée');
            }
            return $rr;
        }
        /** @var float[] */
        $values = [];

        foreach ($this->systeme->emetteurs() as $emetteur) {
            if (null === $rr = $this->table_repository->rr(
                type_emission: $emetteur->type_emission(),
                type_generateur: $generateur->type(),
                label_generateur: $generateur->signaletique()->label,
                reseau_collectif: $generateur->position()->generateur_collectif,
                presence_regulation_terminale: $installation->regulation_terminale()->presence_regulation,
                presence_robinet_thermostatique: $emetteur->presence_robinet_thermostatique(),
            )) {
                throw new \DomainException('Valeur forfaitaire Rr non trouvée');
            }
            $values[] = $rr;
        }
        return array_sum($values) / count($values);
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $systeme->calcule($systeme->data()->with(
                rr: $this->rr(),
            ));
        }
    }
}
