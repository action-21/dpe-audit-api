<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeChaudiere, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Signaletique};
use App\Domain\Common\ValueObject\Id;

final class ChaudiereFactory extends GenerateurFactory
{
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
            type_chaudiere: $signaletique->type_chaudiere ?? TypeChaudiere::CHAUDIERE_SOL,
            pn: $signaletique->pn,
            priorite_cascade: $signaletique->priorite_cascade,
            combustion: $this->energie->is_combustible()
                ? $signaletique->combustion ?? Combustion::create()
                : null,
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_chaudiere() && $energie !== EnergieGenerateur::RESEAU_CHALEUR;
    }
}
