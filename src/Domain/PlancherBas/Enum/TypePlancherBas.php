<?php

namespace App\Domain\PlancherBas\Enum;

use App\Domain\Common\Enum\Enum;

/**
 * Type de plancher bas
 * 
 * TODO: renomage des énumérations
 */
enum TypePlancherBas: int implements Enum
{
    case INCONNU = 1;
    case PLANCHER_AVEC_OU_SANS_REMPLISSAGE = 2;
    case PLANCHER_ENTRE_SOLIVES_METALLIQUES = 3;
    case PLANCHER_ENTRE_SOLIVES_BOIS = 4;
    case PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES = 5;
    case BARDEAUX_ET_REMPLISSAGE = 6;
    case VOUTAINS_SUR_SOLIVES_METALLIQUES = 7;
    case VOUTAINS_BRIQUES_OU_MOELLONS = 8;
    case DALLE_BETON = 9;
    case PLANCHER_BOIS_SUR_SOLIVES_BOIS = 10;
    case PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON = 11;
    case PLANCHER_ENTREVOUS_ISOLANT = 12;
    /** @deprecated*/
    case AUTRES = 13;

    public static function from_enum_type_plancher_bas_id(int $id): self
    {
        return self::from($id);
    }

    /** @inheritdoc */
    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::INCONNU => 'Inconnu',
            self::PLANCHER_AVEC_OU_SANS_REMPLISSAGE => 'Plancher avec ou sans remplissage',
            self::PLANCHER_ENTRE_SOLIVES_METALLIQUES => 'Plancher entre solives métalliques avec ou sans remplissage',
            self::PLANCHER_ENTRE_SOLIVES_BOIS => 'Plancher entre solives bois avec ou sans remplissage',
            self::PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES => 'Plancher bois sur solives métalliques',
            self::BARDEAUX_ET_REMPLISSAGE => 'Bardeaux et remplissage',
            self::VOUTAINS_SUR_SOLIVES_METALLIQUES => 'Voutains sur solives métalliques',
            self::VOUTAINS_BRIQUES_OU_MOELLONS => 'Voutains en briques ou moellons',
            self::DALLE_BETON => 'Dalle béton',
            self::PLANCHER_BOIS_SUR_SOLIVES_BOIS => 'Plancher bois sur solives bois',
            self::PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON => 'Plancher lourd type entrevous terre-cuite, poutrelles béton',
            self::PLANCHER_ENTREVOUS_ISOLANT => 'Plancher à entrevous isolant',
            self::AUTRES => 'Autre type de plancher non répertorié',
        };
    }
}
