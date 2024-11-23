<?php

namespace App\Domain\Audit\ValueObject;

use App\Domain\Audit\Data\SollicitationExterieureCollection;
use App\Domain\Common\Enum\{Mois, ScenarioUsage};

final class Situation
{
    public function __construct(
        public readonly float $tbase,
        public readonly SollicitationExterieureCollection $sollicitations_exterieures,
    ) {}

    public static function create(
        float $tbase,
        SollicitationExterieureCollection $sollicitations_exterieures
    ): self {
        return new self(
            tbase: $tbase,
            sollicitations_exterieures: $sollicitations_exterieures
        );
    }

    /**
     * Ensoleillement en kWh/m²
     */
    public function epv(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures->find($mois)?->epv;
    }

    /**
     * E - Ensoleillement reçupar une paroi verticale orientée au sud en absence d'ombrage sur le mois en kWh/m²
     */
    public function e(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures->find($mois)?->e;
    }

    /**
     * E_fr - Ensoleillement reçu en période de refroidissement sur le mois en kWh/m²
     * 
     * TODO: Vérifier la méthode (coquille ?)
     */
    public function e_fr(ScenarioUsage $scenario, Mois $mois): ?float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures->find($mois)?->efr28,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures->find($mois)?->efr26,
        };
    }

    /**
     * DH - Degrés-heures de chauffage sur le mois en °C.h
     */
    public function dh(ScenarioUsage $scenario, Mois $mois): ?float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures->find($mois)?->dh19,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures->find($mois)?->dh21,
        };
    }

    /**
     * DH14 - Degrés heures de base 14 sur la saison de chauffe complète °C.h
     */
    public function dh14(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures->find($mois)?->dh14;
    }

    /**
     * Nref - Nombre d'heures de chauffage sur le mois en h
     */
    public function nref(ScenarioUsage $scenario, Mois $mois): ?float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures->find($mois)?->nref19,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures->find($mois)?->nref21,
        };
    }

    /**
     * Nref_fr - Nombre d'heures de refroidissement sur le mois en h
     */
    public function nref_fr(ScenarioUsage $scenario, Mois $mois): ?float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures->find($mois)?->nref28,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures->find($mois)?->nref26,
        };
    }

    /**
     * Text- Température extérieure moyenne en période de chauffe sur le mois en C°
     */
    public function text(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures->find($mois)?->text;
    }

    /**
     * Text_fr - Température extérieure moyenne en période de refroidissement sur le mois en C°
     */
    public function text_fr(ScenarioUsage $scenario, Mois $mois): ?float
    {
        return match ($scenario) {
            ScenarioUsage::CONVENTIONNEL => $this->sollicitations_exterieures->find($mois)?->textmoy_clim28,
            ScenarioUsage::DEPENSIER => $this->sollicitations_exterieures->find($mois)?->textmoy_clim26,
        };
    }

    /**
     * tefs - Température moyenne d'eau froide sanitaire sur le mois en °C
     */
    public function tefs(Mois $mois): ?float
    {
        return $this->sollicitations_exterieures->find($mois)?->tefs;
    }

    /**
     * tbase - Température de base de chauffage (°C)
     */
    public function tbase(): float
    {
        return $this->tbase;
    }
}
