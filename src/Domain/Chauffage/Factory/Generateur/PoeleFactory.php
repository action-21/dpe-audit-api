<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Position, Signaletique};
use App\Domain\Common\ValueObject\Id;

final class PoeleFactory extends GenerateurFactory
{
    public function set_position(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id,
    ): static {
        $this->position = Position::create(
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: false,
            generateur_multi_batiment: false,
        );
        return $this;
    }

    public function set_reseau_chaleur(Id $id): static
    {
        return $this;
    }

    public function set_energie_partie_chaudiere(EnergieGenerateur $energie_partie_chaudiere): static
    {
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        $this->signaletique = Signaletique::create(
            pn: $signaletique->pn,
            label: $signaletique->label,
            combustion: $signaletique->combustion ?? Combustion::create(),
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_poele_insert() && ($energie->is_bois() || $energie === EnergieGenerateur::CHARBON);
    }
}
