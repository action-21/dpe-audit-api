<?php

namespace App\Domain\Ecs\Factory\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeChaudiere, TypeGenerateur};
use App\Domain\Ecs\Factory\GenerateurFactory;
use App\Domain\Ecs\ValueObject\Generateur\{Combustion, Signaletique};

final class ChauffeEauFactory extends GenerateurFactory
{
    public function set_reseau_chaleur(Id $id): static
    {
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        if ($signaletique->volume_stockage === 0) {
            $this->type = TypeGenerateur::CHAUFFE_EAU_INSTANTANE;
        } elseif ($this->energie === EnergieGenerateur::ELECTRICITE) {
            $this->type = in_array($this->type, [
                TypeGenerateur::CHAUFFE_EAU_VERTICAL,
                TypeGenerateur::CHAUFFE_EAU_HORIZONTAL,
            ]) ? $this->type : TypeGenerateur::CHAUFFE_EAU_VERTICAL;
        } else {
            $this->type = TypeGenerateur::ACCUMULATEUR;
        }

        $this->signaletique = Signaletique::create(
            volume_stockage: $signaletique->volume_stockage,
            type_chaudiere: $signaletique->type_chaudiere ?? TypeChaudiere::CHAUDIERE_SOL,
            pn: $signaletique->pn,
            label: $signaletique->label,
            combustion: $this->energie->is_combustible()
                ? $signaletique->combustion ?? Combustion::create()
                : null,
        );
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_chauffe_eau() && in_array($energie, [
            EnergieGenerateur::ELECTRICITE,
            EnergieGenerateur::GAZ_NATUREL,
            EnergieGenerateur::GPL,
        ]);
    }
}
