<?php

namespace App\Domain\PlancherBas\Enum;

use App\Domain\Common\Enum\Enum;

enum TypePlancherBas: string implements Enum
{
    case INCONNU = 'INCONNU';
    case PLANCHER_AVEC_OU_SANS_REMPLISSAGE = 'PLANCHER_AVEC_OU_SANS_REMPLISSAGE';
    case PLANCHER_ENTRE_SOLIVES_METALLIQUES = 'PLANCHER_ENTRE_SOLIVES_METALLIQUES';
    case PLANCHER_ENTRE_SOLIVES_BOIS = 'PLANCHER_ENTRE_SOLIVES_BOIS';
    case PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES = 'PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES';
    case BARDEAUX_ET_REMPLISSAGE = 'BARDEAUX_ET_REMPLISSAGE';
    case VOUTAINS_SUR_SOLIVES_METALLIQUES = 'VOUTAINS_SUR_SOLIVES_METALLIQUES';
    case VOUTAINS_BRIQUES_OU_MOELLONS = 'VOUTAINS_BRIQUES_OU_MOELLONS';
    case DALLE_BETON = 'DALLE_BETON';
    case PLANCHER_BOIS_SUR_SOLIVES_BOIS = 'PLANCHER_BOIS_SUR_SOLIVES_BOIS';
    case PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON = 'PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON';
    case PLANCHER_ENTREVOUS_ISOLANT = 'PLANCHER_ENTREVOUS_ISOLANT';

    public static function from_enum_type_plancher_bas_id(int $type_plancher_bas_id): self
    {
        return match ($type_plancher_bas_id) {
            1 => self::INCONNU,
            2 => self::PLANCHER_AVEC_OU_SANS_REMPLISSAGE,
            3 => self::PLANCHER_ENTRE_SOLIVES_METALLIQUES,
            4 => self::PLANCHER_ENTRE_SOLIVES_BOIS,
            5 => self::PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES,
            6 => self::BARDEAUX_ET_REMPLISSAGE,
            7 => self::VOUTAINS_SUR_SOLIVES_METALLIQUES,
            8 => self::VOUTAINS_BRIQUES_OU_MOELLONS,
            9 => self::DALLE_BETON,
            10 => self::PLANCHER_BOIS_SUR_SOLIVES_BOIS,
            11 => self::PLANCHER_LOURD_TYPE_ENTREVOUS_TERRE_CUITE_OU_POUTRELLES_BETON,
            12 => self::PLANCHER_ENTREVOUS_ISOLANT,
        };
    }

    public function id(): string
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
        };
    }

    public function pont_thermique_negligeable(): bool
    {
        return \in_array($this, [
            self::BARDEAUX_ET_REMPLISSAGE,
            self::PLANCHER_BOIS_SUR_SOLIVES_BOIS,
            self::PLANCHER_BOIS_SUR_SOLIVES_METALLIQUES,
        ]);
    }
}
