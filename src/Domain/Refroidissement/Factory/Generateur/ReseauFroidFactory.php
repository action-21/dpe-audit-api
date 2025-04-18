<?php

namespace App\Domain\Refroidissement\Factory\Generateur;

use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Refroidissement\Factory\GenerateurFactory;

final class ReseauFroidFactory extends GenerateurFactory
{
    public function set_seer(float $seer): static
    {
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type === TypeGenerateur::RESEAU_FROID && $energie === EnergieGenerateur::RESEAU_FROID;
    }
}
