<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Position, Signaletique};
use App\Domain\Common\ValueObject\Id;

final class GenerateurAirChaudFactory extends GenerateurFactory
{
    public function set_position(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id,
    ): static {
        $this->position = Position::create(
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment,
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
            combustion: $this->energie->is_combustible()
                ? $signaletique->combustion ?? Combustion::create()
                : null
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_generateur_air_chaud() && $energie !== EnergieGenerateur::RESEAU_CHALEUR;
    }
}
