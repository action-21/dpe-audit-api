<?php

namespace App\Engine\Performance\Chauffage\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{ConfigurationSysteme as Configuration};
use App\Engine\Performance\Rule;

abstract class DimensionnementSysteme extends Rule
{
    protected Audit $audit;
    protected Systeme $systeme;

    /**
     * @see \App\Engine\Performance\Chauffage\Dimensionnement\ConfigurationSysteme::configuration()
     */
    public function configuration(): Configuration
    {
        return $this->systeme->data()->configuration;
    }

    /**
     * Présence d'un système en base
     */
    public function has_configuration(Configuration $configuration): bool
    {
        return $this->systeme->installation()->systemes()->with_configuration($configuration)->count() > 0;
    }

    abstract public static function match(Systeme $systeme): bool;

    abstract public function rdim(): float;

    public function apply(Audit $entity): void
    {
        $this->audit = $entity;
        foreach ($entity->chauffage()->systemes() as $systeme) {
            if (!static::match($systeme)) {
                continue;
            }
            $this->systeme = $systeme;

            $systeme->calcule($systeme->data()->with(
                rdim: $this->rdim(),
            ));
        }
    }

    public static function dependencies(): array
    {
        return [
            ConfigurationSysteme::class,
        ];
    }
}
