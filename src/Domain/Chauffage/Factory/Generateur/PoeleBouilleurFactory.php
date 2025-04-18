<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Signaletique};
use App\Domain\Common\ValueObject\Id;

final class PoeleBouilleurFactory extends GenerateurFactory
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
            pn: $signaletique->pn,
            combustion: $signaletique->combustion ?? Combustion::create(),
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type === TypeGenerateur::POELE_BOUILLEUR && $energie->is_bois();
    }
}
