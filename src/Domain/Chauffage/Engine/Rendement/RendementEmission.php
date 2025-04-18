<?php

namespace App\Domain\Chauffage\Engine\Rendement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\TypeEmission;
use App\Domain\Chauffage\Service\ChauffageTableValeurRepository;
use App\Domain\Common\EngineRule;

final class RendementEmission extends EngineRule
{
    protected Systeme $systeme;

    public function __construct(protected readonly ChauffageTableValeurRepository $table_repository) {}

    /**
     * Rendement d'émission
     */
    public function re(): float
    {
        if ($this->systeme->emetteurs()->count() === 0) {
            if (null === $re = $this->table_repository->re(
                type_emission: TypeEmission::from_type_generateur($this->systeme->generateur()->type()),
                type_generateur: $this->systeme->generateur()->type(),
                label_generateur: $this->systeme->generateur()->signaletique()->label,
            )) {
                throw new \DomainException('Valeur forfaitaire Re non trouvée');
            }
            return $re;
        }
        /** @var float[] */
        $values = [];

        foreach ($this->systeme->emetteurs() as $emetteur) {
            if (null === $re = $this->table_repository->re(
                type_emission: $emetteur->type_emission(),
                type_generateur: $this->systeme->generateur()->type(),
                label_generateur: $this->systeme->generateur()->signaletique()->label,
            )) {
                throw new \DomainException('Valeur forfaitaire Re non trouvée');
            }
            $values[] = $re;
        }
        return array_sum($values) / count($values);
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $systeme->calcule($systeme->data()->with(
                re: $this->re(),
            ));
        }
    }
}
