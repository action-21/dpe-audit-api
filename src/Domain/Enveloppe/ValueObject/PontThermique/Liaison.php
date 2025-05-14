<?php

namespace App\Domain\Enveloppe\ValueObject\PontThermique;

use App\Domain\Enveloppe\Entity\{Baie, Mur, Paroi, PlancherBas, PlancherHaut, Porte};
use App\Domain\Enveloppe\Enum\PontThermique\TypeLiaison;

final class Liaison
{
    public function __construct(
        public readonly TypeLiaison $type,
        public readonly bool $pont_thermique_partiel,
        public readonly Mur $mur,
        public readonly ?Paroi $paroi,
    ) {}

    public static function create(
        TypeLiaison $type,
        Mur $mur,
        ?Paroi $paroi,
        bool $pont_thermique_partiel = false,
    ): self {
        return match ($type) {
            TypeLiaison::PLANCHER_BAS_MUR => self::create_liaison_plancher_bas_mur(
                mur: $mur,
                paroi: $paroi,
            ),
            TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR => self::create_liaison_plancher_intermediaire_mur(
                mur: $mur,
                pont_thermique_partiel: $pont_thermique_partiel,
            ),
            TypeLiaison::PLANCHER_HAUT_MUR => self::create_liaison_plancher_haut_mur(
                mur: $mur,
                paroi: $paroi,
            ),
            TypeLiaison::REFEND_MUR => self::create_liaison_refend_mur(
                mur: $mur,
                pont_thermique_partiel: $pont_thermique_partiel,
            ),
            TypeLiaison::MENUISERIE_MUR => self::create_liaison_menuiserie_mur(
                mur: $mur,
                paroi: $paroi,
            ),
        };
    }

    public static function create_liaison_plancher_bas_mur(
        Mur $mur,
        Paroi $paroi,
    ): self {
        return new self(
            type: TypeLiaison::PLANCHER_BAS_MUR,
            mur: $mur,
            paroi: $paroi,
            pont_thermique_partiel: false,
        );
    }

    public static function create_liaison_plancher_intermediaire_mur(
        Mur $mur,
        bool $pont_thermique_partiel,
    ): self {
        return new self(
            type: TypeLiaison::PLANCHER_INTERMEDIAIRE_MUR,
            mur: $mur,
            paroi: null,
            pont_thermique_partiel: $pont_thermique_partiel,
        );
    }

    public static function create_liaison_plancher_haut_mur(
        Mur $mur,
        Paroi $paroi,
    ): self {
        return new self(
            type: TypeLiaison::PLANCHER_HAUT_MUR,
            mur: $mur,
            paroi: $paroi,
            pont_thermique_partiel: false,
        );
    }

    public static function create_liaison_refend_mur(
        Mur $mur,
        bool $pont_thermique_partiel,
    ): self {
        return new self(
            type: TypeLiaison::REFEND_MUR,
            mur: $mur,
            paroi: null,
            pont_thermique_partiel: $pont_thermique_partiel,
        );
    }

    public static function create_liaison_menuiserie_mur(
        Mur $mur,
        Paroi $paroi,
    ): self {
        return new self(
            type: TypeLiaison::MENUISERIE_MUR,
            mur: $mur,
            paroi: $paroi,
            pont_thermique_partiel: false,
        );
    }

    public function plancher(): null|PlancherBas|PlancherHaut
    {
        return in_array($this->type, [
            TypeLiaison::PLANCHER_BAS_MUR,
            TypeLiaison::PLANCHER_HAUT_MUR,
        ]) ? $this->paroi : null;
    }

    public function plancher_bas(): ?PlancherBas
    {
        return $this->type === TypeLiaison::PLANCHER_BAS_MUR ? $this->paroi : null;
    }

    public function plancher_haut(): ?PlancherHaut
    {
        return $this->type === TypeLiaison::PLANCHER_HAUT_MUR ? $this->paroi : null;
    }

    public function menuiserie(): null|Baie|Porte
    {
        return $this->type === TypeLiaison::MENUISERIE_MUR ? $this->paroi : null;
    }
}
