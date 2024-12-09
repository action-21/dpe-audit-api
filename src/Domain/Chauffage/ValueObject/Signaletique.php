<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, PositionChaudiere, TypeCombustion, TypeGenerateur};
use Webmozart\Assert\Assert;

final class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly ?EnergieGenerateur $energie_partie_chaudiere = null,
        public readonly ?PositionChaudiere $position_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?int $priorite_cascade = null,
        public readonly ?float $pn = null,
        public readonly ?float $scop = null,
        public readonly ?Combustion $combustion = null,
    ) {}

    public static function create_chaudiere(
        TypeGenerateur\Chaudiere $type,
        EnergieGenerateur\Chaudiere $energie,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?PositionChaudiere $position,
        ?Combustion $combustion,
        ?int $priorite_cascade,
        ?float $pn,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThanEq($priorite_cascade, 0);

        $position = $position ?? PositionChaudiere::CHAUDIERE_SOL;
        $priorite_cascade = $priorite_cascade > 2 ? 2 : $priorite_cascade;

        if ($type->to() === TypeGenerateur::CHAUDIERE_MULTI_BATIMENT) {
            $position_volume_chauffe = false;
            $generateur_collectif = true;
        }
        if ($energie->to() === EnergieGenerateur::ELECTRICITE) {
            $combustion = null;
        }
        if ($energie->to() !== EnergieGenerateur::ELECTRICITE) {
            $combustion = $combustion ?? Combustion::default();
        }

        return new self(
            type: $type->to(),
            energie: $energie->to(),
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            priorite_cascade: $priorite_cascade,
            position_chaudiere: $position,
            pn: $pn,
            combustion: $combustion,
        );
    }

    public static function create_chauffage_electrique(
        TypeGenerateur\ChauffageElectrique $type,
        LabelGenerateur\ChauffageElectrique $label,
        ?float $pn,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);

        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            label: $label->to(),
            position_volume_chauffe: true,
            generateur_collectif: false,
            pn: $pn
        );
    }

    public static function create_pac(
        TypeGenerateur\Pac $type,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?float $scop,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($scop, 0);

        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            scop: $scop,
        );
    }

    public static function create_pac_hybride(
        TypeGenerateur\Pac $type,
        EnergieGenerateur\PacHybride $energie_partie_chaudiere,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?PositionChaudiere $position,
        ?Combustion $combustion,
        ?int $priorite_cascade,
        ?float $pn,
        ?float $scop,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($scop, 0);
        Assert::nullOrGreaterThanEq($priorite_cascade, 0);

        $position = $position ?? PositionChaudiere::CHAUDIERE_SOL;
        $combustion = $combustion ?? Combustion::default();
        $priorite_cascade = $priorite_cascade > 2 ? 2 : $priorite_cascade;

        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            energie_partie_chaudiere: $energie_partie_chaudiere->to(),
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            priorite_cascade: $priorite_cascade,
            position_chaudiere: $position,
            pn: $pn,
            scop: $scop,
            combustion: $combustion,
        );
    }

    public static function create_generateur_air_chaud(
        TypeGenerateur\GenerateurAirChaud $type,
        EnergieGenerateur\GenerateurAirChaud $energie,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?Combustion $combustion,
        ?float $pn,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);

        if ($energie->to() === EnergieGenerateur::ELECTRICITE) {
            $combustion = null;
        }
        if ($energie->to() !== EnergieGenerateur::ELECTRICITE) {
            $combustion = $combustion ?? Combustion::default();
        }
        return new self(
            type: $type->to(),
            energie: $energie->to(),
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            combustion: $combustion,
            pn: $pn,
        );
    }

    public static function create_poele_insert(
        TypeGenerateur\PoeleInsert $type,
        EnergieGenerateur\PoeleInsert $energie,
        LabelGenerateur\PoeleInsert $label,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);

        return new self(
            type: $type->to(),
            energie: $energie->to(),
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            label: $label->to(),
            pn: $pn,
        );
    }

    public static function create_poele_bouilleur(
        TypeGenerateur\PoeleBouilleur $type,
        EnergieGenerateur\PoeleBouilleur $energie,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?Combustion $combustion,
        ?float $pn,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);

        return new self(
            type: $type->to(),
            energie: $energie->to(),
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            combustion: $combustion ?? Combustion::default(),
            pn: $pn,
        );
    }

    public static function create_reseau_chaleur(): self
    {
        return new self(
            type: TypeGenerateur::RESEAU_CHALEUR,
            energie: EnergieGenerateur::RESEAU_CHALEUR,
            position_volume_chauffe: false,
            generateur_collectif: true,
        );
    }

    public static function create_radiateur_gaz(
        TypeGenerateur\RadiateurGaz $type,
        EnergieGenerateur\RadiateurGaz $energie,
        ?float $rpn,
        ?float $pn,
    ): static {
        Assert::nullOrGreaterThan($pn, 0);

        return new self(
            type: $type->to(),
            energie: $energie->to(),
            position_volume_chauffe: true,
            generateur_collectif: false,
            combustion: $combustion ?? Combustion::create(type: TypeCombustion::STANDARD, rpn: $rpn),
            pn: $pn,
        );
    }

    public function effet_joule(): bool
    {
        return \in_array($this->type, [
            TypeGenerateur::CHAUDIERE,
            TypeGenerateur::CHAUDIERE_MULTI_BATIMENT,
            TypeGenerateur::GENERATEUR_AIR_CHAUD,
            TypeGenerateur::CONVECTEUR_BI_JONCTION,
            TypeGenerateur::CONVECTEUR_ELECTRIQUE,
            TypeGenerateur::PANNEAU_RAYONNANT_ELECTRIQUE,
            TypeGenerateur::PLAFOND_RAYONNANT_ELECTRIQUE,
            TypeGenerateur::PLANCHER_RAYONNANT_ELECTRIQUE,
            TypeGenerateur::RADIATEUR_ELECTRIQUE,
            TypeGenerateur::RADIATEUR_ELECTRIQUE_ACCUMULATION,
            TypeGenerateur::GENERATEUR_AIR_CHAUD,
        ]) && $this->energie === EnergieGenerateur::ELECTRICITE;
    }
}
