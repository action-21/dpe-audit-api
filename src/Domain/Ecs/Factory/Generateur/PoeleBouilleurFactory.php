<?php

namespace App\Domain\Ecs\Factory\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\Factory\GenerateurFactory;
use App\Domain\Ecs\ValueObject\Generateur\{Combustion, Signaletique};

final class PoeleBouilleurFactory extends GenerateurFactory
{
    public function set_reseau_chaleur(Id $id): static
    {
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        $this->signaletique = Signaletique::create(
            volume_stockage: $signaletique->volume_stockage,
            pn: $signaletique->pn,
            combustion: $signaletique->combustion ?? Combustion::create(),
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_chaudiere() && $energie->is_bois();
    }
}
