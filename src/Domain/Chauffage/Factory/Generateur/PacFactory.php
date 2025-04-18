<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeChaudiere, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Signaletique};
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class PacFactory extends GenerateurFactory
{
    public function set_reseau_chaleur(Id $id): static
    {
        return $this;
    }

    public function set_energie_partie_chaudiere(EnergieGenerateur $energie_partie_chaudiere): static
    {
        if ($this->type->is_pac_hybride()) {
            Assert::true($energie_partie_chaudiere->is_combustible());
            $this->energie_partie_chaudiere = $energie_partie_chaudiere;
        }
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        $this->signaletique = Signaletique::create(
            type_chaudiere: $signaletique->type_chaudiere ?? TypeChaudiere::CHAUDIERE_SOL,
            pn: $signaletique->pn,
            scop: $signaletique->scop,
            combustion: $this->energie_partie_chaudiere?->is_combustible()
                ? $signaletique->combustion ?? Combustion::create()
                : null,
        );
        return $this;
    }

    public function build(): Generateur
    {
        if ($this->type->is_pac_hybride()) {
            Assert::notNull($this->energie_partie_chaudiere);
        }
        return parent::build();
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_pac() && $energie !== EnergieGenerateur::RESEAU_CHALEUR;
    }
}
