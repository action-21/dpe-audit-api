<?php

namespace App\Domain\Ecs\Factory\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\Factory\GenerateurFactory;
use App\Domain\Ecs\ValueObject\Generateur\{Position, Signaletique};

final class ReseauChaleurFactory extends GenerateurFactory
{
    public function set_position(
        bool $generateur_collectif,
        bool $position_volume_chauffe,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id,
    ): static {
        $this->position = Position::create(
            generateur_collectif: true,
            position_volume_chauffe: false,
            generateur_multi_batiment: true,
        );
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        $this->signaletique = Signaletique::create(volume_stockage: 0);
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type === TypeGenerateur::RESEAU_CHALEUR && $energie === EnergieGenerateur::RESEAU_CHALEUR;
    }
}
