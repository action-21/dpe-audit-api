<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Position, Signaletique};
use App\Domain\Common\ValueObject\Id;

final class ReseauChaleurFactory extends GenerateurFactory
{
    public function set_position(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id,
    ): static {
        $this->position = Position::create(
            position_volume_chauffe: false,
            generateur_collectif: true,
            generateur_multi_batiment: true,
            generateur_mixte_id: $generateur_mixte_id,
        );
        return $this;
    }

    public function set_energie_partie_chaudiere(EnergieGenerateur $energie_partie_chaudiere): static
    {
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        $this->signaletique = Signaletique::create();
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type !== TypeGenerateur::RESEAU_CHALEUR && $energie !== EnergieGenerateur::RESEAU_CHALEUR;
    }
}
