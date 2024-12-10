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
        public readonly ?PositionChaudiere $position_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?float $pn = null,
        public readonly ?float $cop = null,
        public readonly ?Combustion $combustion = null,
    ) {}

    private function merge(
        ?PositionChaudiere $position_chaudiere = null,
        ?LabelGenerateur $label = null,
        ?float $pn = null,
        ?float $cop = null,
        ?Combustion $combustion = null,
    ): self {
        return new self(
            type: $this->type,
            energie: $this->energie,
            volume_stockage: $this->volume_stockage,
            position_chaudiere: $position_chaudiere ?? $this->position_chaudiere,
            label: $label ?? $this->label,
            pn: $pn ?? $this->pn,
            cop: $cop ?? $this->cop,
            combustion: $combustion ?? $this->combustion,
        );
    }

    public static function create_chaudiere(
        TypeGenerateur\Chaudiere $type,
        EnergieGenerateur\Chaudiere $energie,
        int $volume_stockage,
    ): self {
        Assert::greaterThanEq($volume_stockage, 0);
        return new self(
            type: $type->to(),
            energie: ($energie = $energie->to()),
            volume_stockage: $volume_stockage,
            position_chaudiere: PositionChaudiere::CHAUDIERE_SOL,
            combustion: $energie->is_combustible() ? Combustion::default() : null,
        );
    }

    public static function create_chauffe_eau(
        TypeGenerateur\ChauffeEau $type,
        EnergieGenerateur\ChauffeEau $energie,
        int $volume_stockage,
    ): static {
        Assert::greaterThanEq($volume_stockage, 0);

        $type = $type->to();
        $energie = $energie->to();

        if ($volume_stockage === 0) {
            $type = TypeGenerateur::CHAUFFE_EAU_INSTANTANE;
        } elseif ($type === TypeGenerateur::CHAUFFE_EAU_INSTANTANE) {
            $type = $energie === EnergieGenerateur::ELECTRICITE ? TypeGenerateur::CHAUFFE_EAU_VERTICAL : TypeGenerateur::ACCUMULATEUR;
        }

        return new self(
            type: $type,
            energie: $energie,
            volume_stockage: $volume_stockage,
            label: LabelGenerateur::SANS,
            combustion: $energie->is_combustible() ? Combustion::default() : null,
        );
    }

    public static function create_pac(
        TypeGenerateur\Pac $type,
        int $volume_stockage,
    ): self {
        Assert::greaterThanEq($volume_stockage, 0);

        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            volume_stockage: $volume_stockage,
        );
    }

    public static function create_poele_bouilleur(
        TypeGenerateur\PoeleBouilleur $type,
        EnergieGenerateur\PoeleBouilleur $energie,
        int $volume_stockage,
    ): self {
        Assert::greaterThanEq($volume_stockage, 0);

        return new self(
            type: $type->to(),
            energie: $energie->to(),
            volume_stockage: $volume_stockage,
            combustion: Combustion::default(),
        );
    }

    public static function create_reseau_chaleur(): self
    {
        return new self(
            type: TypeGenerateur::RESEAU_CHALEUR,
            energie: EnergieGenerateur::RESEAU_CHALEUR,
            volume_stockage: 0,
        );
    }

    public function with_pn(?float $pn): self
    {
        Assert::nullOrGreaterThan($pn, 0);
        return $this->merge(pn: $pn);
    }

    public function with_cop(?float $cop): self
    {
        Assert::nullOrGreaterThan($cop, 0);
        return $this->type->is_pac() ? $this->merge(cop: $cop) : $this;
    }

    public function with_label(?LabelGenerateur $label): self
    {
        return $this->type->is_chauffe_eau() ? $this->merge(label: $label ?? LabelGenerateur::SANS) : $this;
    }

    public function with_position(PositionChaudiere $position_chaudiere): self
    {
        return $this->type->is_chaudiere() ? $this->merge(position_chaudiere: $position_chaudiere) : $this;
    }

    public function with_combustion(?Combustion $combustion): self
    {
        return $this->energie->is_combustible() ? $this->merge(combustion: $combustion ?? Combustion::default()) : $this;
    }
}
