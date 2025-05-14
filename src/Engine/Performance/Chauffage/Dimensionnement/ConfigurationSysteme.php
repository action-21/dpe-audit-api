<?php

namespace App\Engine\Performance\Chauffage\Dimensionnement;

use App\Domain\Audit\Audit;
use App\Domain\Chauffage\Entity\Systeme;
use App\Domain\Chauffage\Enum\{ConfigurationSysteme as Configuration, TypeChauffage};
use App\Engine\Performance\Rule;

final class ConfigurationSysteme extends Rule
{
    private Systeme $systeme;

    /**
     * Configuration du système
     * 
     * - Un système divisé est considéré comme un système d'appoint s'il est associé à un système
     * central. Dans le cas contraire, tous les systèmes sont considérés en base.
     * 
     * - Un système central bois est toujours considéré en base
     * 
     * - Un système central PAC est considéré en relève s'il est associé à un système central bois. 
     * Dans le cas contraire, il est considéré en base.
     * 
     * - Un système central est considéré en relève s'il est associé à un système central bois ou PAC.
     */
    public function configuration(): Configuration
    {
        $installation = $this->systeme->installation();
        $generateur = $this->systeme->generateur();
        $systemes_chauffage_central = $installation->systemes()->with_type(TypeChauffage::CHAUFFAGE_CENTRAL);

        // Un système divisé est considéré comme un système d'appoint s'il est associé à un système 
        // central. Dans le cas contraire, tous les systèmes sont considérés en base.
        if ($this->systeme->type_chauffage()->is(TypeChauffage::CHAUFFAGE_DIVISE)) {
            return $installation->systemes()->has_systeme_central()
                ? Configuration::APPOINT
                : Configuration::BASE;
        }
        // Un système central bois est toujours considéré en base
        if ($generateur->energie()->is_bois()) {
            return Configuration::BASE;
        }
        // Un système central PAC est considéré en relève s'il est associé à un système central bois.
        // Dans le cas contraire, il est considéré en base.
        if ($generateur->type()->is_pac()) {
            return $systemes_chauffage_central->has_chaudiere_bois()
                ? Configuration::RELEVE
                : Configuration::BASE;
        }
        // Un système central est considéré en relève s'il est associé à un système central bois ou PAC
        if ($systemes_chauffage_central->has_chaudiere_bois() || $systemes_chauffage_central->has_pac()) {
            return Configuration::RELEVE;
        }
        return Configuration::BASE;
    }

    public function apply(Audit $entity): void
    {
        foreach ($entity->chauffage()->systemes() as $systeme) {
            $this->systeme = $systeme;

            $systeme->calcule($systeme->data()->with(
                configuration: $this->configuration(),
            ));
        }
    }
}
