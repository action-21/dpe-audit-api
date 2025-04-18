<?php

namespace App\Domain\Ecs\Factory\Generateur;

use App\Domain\Chauffage\Enum\TypeChaudiere;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\Factory\GenerateurFactory;
use App\Domain\Ecs\ValueObject\Generateur\{Combustion, Signaletique};

final class ChaudiereFactory extends GenerateurFactory
{
    public function set_reseau_chaleur(Id $id): static
    {
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        $combustion = $this->energie->is_combustible()
            ? $signaletique->combustion ?? Combustion::create()
            : null;

        $this->signaletique = Signaletique::create(
            volume_stockage: $signaletique->volume_stockage,
            type_chaudiere: $signaletique->type_chaudiere ?? TypeChaudiere::CHAUDIERE_SOL,
            pn: $signaletique->pn,
            combustion: $combustion,
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_chaudiere() && $energie !== EnergieGenerateur::RESEAU_CHALEUR;
    }
}