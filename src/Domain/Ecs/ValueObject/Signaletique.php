<?php

namespace App\Domain\Ecs\ValueObject;

use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, PositionChaudiere, TypeGenerateur};
use Webmozart\Assert\Assert;

final class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        public readonly int $volume_stockage,
        public readonly bool $position_volume_chauffe,
        public readonly bool $generateur_collectif,
        public readonly ?PositionChaudiere $position_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?float $pn = null,
        public readonly ?float $cop = null,
        public readonly ?Combustion $combustion = null,
    ) {}

    public static function create_chaudiere(
        TypeGenerateur\Chaudiere $type,
        EnergieGenerateur\Chaudiere $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?PositionChaudiere $position,
        ?Combustion $combustion,
    ): static {
        Assert::greaterThanEq($volume_stockage, 0);
        Assert::nullOrGreaterThan($pn, 0);

        $position = $position ?? PositionChaudiere::CHAUDIERE_SOL;

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
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            position_chaudiere: $position,
            pn: $pn,
            combustion: $combustion,
        );
    }

    public static function create_chauffe_eau(
        TypeGenerateur\ChauffeEau $type,
        EnergieGenerateur\ChauffeEau $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?LabelGenerateur $label,
        ?Combustion $combustion,
    ): static {
        Assert::greaterThanEq($volume_stockage, 0);
        Assert::nullOrGreaterThan($pn, 0);

        $type = $type->to();
        $energie = $energie->to();

        if ($volume_stockage === 0) {
            $type = TypeGenerateur::CHAUFFE_EAU_INSTANTANE;
        } elseif ($type === TypeGenerateur::CHAUFFE_EAU_INSTANTANE) {
            $type = $energie === EnergieGenerateur::ELECTRICITE
                ? TypeGenerateur::CHAUFFE_EAU_VERTICAL
                : TypeGenerateur::ACCUMULATEUR;
        }

        if ($energie === EnergieGenerateur::ELECTRICITE) {
            $combustion = null;
            $label = $label ?? LabelGenerateur::INCONNU;
        } else {
            $combustion = $combustion ?? Combustion::default();
            $label = null;
        }
        return new self(
            type: $type,
            energie: $energie,
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            label: $label,
            combustion: $combustion,
        );
    }

    public static function create_pac(
        TypeGenerateur\Pac $type,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?float $cop,
    ): static {
        Assert::greaterThanEq($volume_stockage, 0);
        Assert::nullOrGreaterThan($pn, 0);
        Assert::nullOrGreaterThan($cop, 0);

        if ($type->to() === TypeGenerateur::PAC_MULTI_BATIMENT) {
            $position_volume_chauffe = false;
            $generateur_collectif = true;
        }
        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            cop: $cop,
        );
    }

    public static function create_poele_bouilleur(
        TypeGenerateur\PoeleBouilleur $type,
        EnergieGenerateur\PoeleBouilleur $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?Combustion $combustion,
    ): static {
        Assert::greaterThanEq($volume_stockage, 0);
        Assert::nullOrGreaterThan($pn, 0);

        return new self(
            type: $type->to(),
            energie: $energie->to(),
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            combustion: $combustion ?? Combustion::default(),
        );
    }

    public static function create_reseau_chaleur(TypeGenerateur\ReseauChaleur $type): static
    {
        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::RESEAU_CHALEUR,
            volume_stockage: 0,
            position_volume_chauffe: false,
            generateur_collectif: true,
        );
    }
}
