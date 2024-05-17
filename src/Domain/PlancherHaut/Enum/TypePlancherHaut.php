<?php

namespace App\Domain\PlancherHaut\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * Type de plancher haut
 */
enum TypePlancherHaut: int implements Enum
{
    case INCONNU = 1;
    case PLAFOND_AVEC_OU_SANS_REMPLISSAGE = 2;
    case PLAFOND_ENTRE_SOLIVES_METALLIQUES = 3;
    case PLAFOND_ENTRE_SOLIVES_BOIS = 4;
    case PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES = 5;
    case PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES = 6;
    case BARDEAUX_ET_REMPLISSAGE = 7;
    case DALLE_BETON = 8;
    case PLAFOND_BOIS_SUR_SOLIVES_BOIS = 9;
    case PLAFOND_BOIS_SOUS_SOLIVES_BOIS = 10;
    case PLAFOND_LOURD = 11;
    case COMBLES_AMENAGES_SOUS_RAMPANT = 12;
    case TOITURE_CHAUME = 13;
    case PLAFOND_PATRE = 14;
    case BAC_ACIER = 15;

    /** @deprecated */
    case AUTRES = 16;

    public static function from_enum_type_plancher_haut_id(int $id): self
    {
        return self::from($id);
    }

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
            self::PLAFOND_AVEC_OU_SANS_REMPLISSAGE => 'Plafond avec ou sans remplissage',
            self::PLAFOND_ENTRE_SOLIVES_METALLIQUES => 'Plafond entre solives métalliques avec ou sans remplissage',
            self::PLAFOND_ENTRE_SOLIVES_BOIS => 'Plafond entre solives bois avec ou sans remplissage',
            self::PLAFOND_BOIS_SUR_SOLIVES_METALLIQUES => 'Plafond bois sur solives métalliques',
            self::PLAFOND_BOIS_SOUS_SOLIVES_METALLIQUES => 'Plafond bois sous solives métalliques',
            self::BARDEAUX_ET_REMPLISSAGE => 'Bardeaux et remplissage',
            self::DALLE_BETON => 'Dalle béton',
            self::PLAFOND_BOIS_SUR_SOLIVES_BOIS => 'Plafond bois sur solives bois',
            self::PLAFOND_BOIS_SOUS_SOLIVES_BOIS => 'Plafond bois sous solives bois',
            self::PLAFOND_LOURD => 'Plafond lourd type entrevous terre-cuite, poutrelles béton',
            self::COMBLES_AMENAGES_SOUS_RAMPANT => 'Combles aménagés sous rampant',
            self::TOITURE_CHAUME => 'Toiture en chaume',
            self::PLAFOND_PATRE => 'Plafond en plaque de plâtre',
            self::BAC_ACIER => 'Toitures en Bac acier',
            self::AUTRES => 'Autre type de plafond non répertorié',
        };
    }

    /**
     * Le type de plancher haut est compatible avec une 5ème façade
     */
    public function facade(): bool
    {
        return match ($this) {
            self::COMBLES_AMENAGES_SOUS_RAMPANT, self::TOITURE_CHAUME, self::BAC_ACIER => true,
            default => false,
        };
    }

    /**
     * Configuration du plancher haut
     * 
     * @return array<ConfigurationPlancherHaut>
     */
    public function configurations_applicables(): array
    {
        return match ($this) {
            self::COMBLES_AMENAGES_SOUS_RAMPANT, self::TOITURE_CHAUME, self::BAC_ACIER => [ConfigurationPlancherHaut::COMBLES_HABITABLES],
            default => [ConfigurationPlancherHaut::COMBLES_PERDUS, ConfigurationPlancherHaut::TOITURE_TERRASSE],
        };
    }

    public function configuration_plancher_haut(): ?ConfigurationPlancherHaut
    {
        return match ($this) {
            self::COMBLES_AMENAGES_SOUS_RAMPANT, self::TOITURE_CHAUME, self::BAC_ACIER => ConfigurationPlancherHaut::COMBLES_HABITABLES,
            default => null,
        };
    }
}
