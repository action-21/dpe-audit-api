<?php

namespace App\Domain\Chauffage\ValueObject;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, PositionChaudiere, TypeGenerateur};
use Webmozart\Assert\Assert;

final class Signaletique
{
    public function __construct(
        public readonly TypeGenerateur $type,
        public readonly EnergieGenerateur $energie,
        public readonly ?TypeGenerateur $type_partie_chaudiere = null,
        public readonly ?EnergieGenerateur $energie_partie_chaudiere = null,
        public readonly ?PositionChaudiere $position_chaudiere = null,
        public readonly ?LabelGenerateur $label = null,
        public readonly ?int $priorite_cascade = null,
        public readonly ?float $pn = null,
        public readonly ?float $scop = null,
        public readonly ?Combustion $combustion = null,
    ) {}

    private function merge(
        ?TypeGenerateur $type_partie_chaudiere = null,
        ?EnergieGenerateur $energie_partie_chaudiere = null,
        ?PositionChaudiere $position_chaudiere = null,
        ?LabelGenerateur $label = null,
        ?int $priorite_cascade = null,
        ?float $pn = null,
        ?float $scop = null,
        ?Combustion $combustion = null,
    ): self {
        return new self(
            type: $this->type,
            energie: $this->energie,
            type_partie_chaudiere: $type_partie_chaudiere ?? $this->type_partie_chaudiere,
            energie_partie_chaudiere: $energie_partie_chaudiere ?? $this->energie_partie_chaudiere,
            position_chaudiere: $position_chaudiere ?? $this->position_chaudiere,
            label: $label ?? $this->label,
            priorite_cascade: $priorite_cascade ?? $this->priorite_cascade,
            pn: $pn ?? $this->pn,
            scop: $scop ?? $this->scop,
            combustion: $combustion ?? $this->combustion,
        );
    }

    public static function create_chaudiere(
        TypeGenerateur\Chaudiere $type,
        EnergieGenerateur\Chaudiere $energie,
    ): static {
        return new self(
            type: $type->to(),
            energie: ($energie = $energie->to()),
            position_chaudiere: PositionChaudiere::CHAUDIERE_SOL,
            combustion: $energie->is_combustible() ? Combustion::default() : null,
        );
    }

    public static function create_chauffage_electrique(
        TypeGenerateur\ChauffageElectrique $type,
        LabelGenerateur\ChauffageElectrique $label,
    ): self {
        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            label: $label->to(),
        );
    }

    public static function create_pac(TypeGenerateur\Pac $type,): self
    {
        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
        );
    }

    public static function create_pac_hybride(
        TypeGenerateur\PacHybride $type,
        EnergieGenerateur\PacHybride $energie_partie_chaudiere,
    ): self {
        return new self(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            type_partie_chaudiere: TypeGenerateur::CHAUDIERE,
            energie_partie_chaudiere: $energie_partie_chaudiere->to(),
            position_chaudiere: PositionChaudiere::CHAUDIERE_SOL,
            combustion: Combustion::default(),
        );
    }

    public static function create_generateur_air_chaud(
        TypeGenerateur\GenerateurAirChaud $type,
        EnergieGenerateur\GenerateurAirChaud $energie,
    ): self {
        return new self(
            type: $type->to(),
            energie: ($energie = $energie->to()),
            combustion: $energie->is_combustible() ? Combustion::default() : null,
        );
    }

    public static function create_poele_insert(
        TypeGenerateur\PoeleInsert $type,
        EnergieGenerateur\PoeleInsert $energie,
        LabelGenerateur\PoeleInsert $label,
    ): self {
        return new self(
            type: $type->to(),
            energie: $energie->to(),
            label: $label->to(),
        );
    }

    public static function create_poele_bouilleur(
        TypeGenerateur\PoeleBouilleur $type,
        EnergieGenerateur\PoeleBouilleur $energie,
    ): self {
        return new self(
            type: $type->to(),
            energie: $energie->to(),
            combustion: Combustion::default(),
        );
    }

    public static function create_reseau_chaleur(): self
    {
        return new self(
            type: TypeGenerateur::RESEAU_CHALEUR,
            energie: EnergieGenerateur::RESEAU_CHALEUR,
        );
    }

    public static function create_radiateur_gaz(
        TypeGenerateur\RadiateurGaz $type,
        EnergieGenerateur\RadiateurGaz $energie,
    ): self {
        return new self(
            type: $type->to(),
            energie: $energie->to(),
            combustion: Combustion::default(),
        );
    }

    public function with_pn(?float $pn): self
    {
        Assert::nullOrGreaterThan($pn, 0);
        return $this->merge(pn: $pn);
    }

    public function with_scop(?float $scop): self
    {
        Assert::nullOrGreaterThan($scop, 0);
        return $this->type->is_pac() ? $this->merge(scop: $scop) : $this;
    }

    public function with_position(PositionChaudiere $position_chaudiere): self
    {
        return $this->type->is_chaudiere() ? $this->merge(position_chaudiere: $position_chaudiere) : $this;
    }

    public function with_cascade(?int $priorite_cascade): self
    {
        Assert::nullOrGreaterThanEq($priorite_cascade, 0);
        return $this->type->is_chaudiere() ? $this->merge(priorite_cascade: $priorite_cascade) : $this;
    }

    public function with_combustion(?Combustion $combustion): self
    {
        return $this->energie->is_combustible() ? $this->merge(combustion: $combustion ?? Combustion::default()) : $this;
    }

    public function effet_joule(): bool
    {
        return $this->energie === EnergieGenerateur::ELECTRICITE;
    }
}
