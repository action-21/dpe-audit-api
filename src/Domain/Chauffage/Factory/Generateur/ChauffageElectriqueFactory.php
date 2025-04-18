<?php

namespace App\Domain\Chauffage\Factory\Generateur;

use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\Signaletique;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class ChauffageElectriqueFactory extends GenerateurFactory
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
        Assert::nullOrInArray($signaletique->label, [LabelGenerateur::NF_PERFORMANCE]);

        $this->signaletique = Signaletique::create(
            pn: $signaletique->pn,
            label: $signaletique->label,
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_chauffage_electrique() && $energie === EnergieGenerateur::ELECTRICITE;
    }
}
