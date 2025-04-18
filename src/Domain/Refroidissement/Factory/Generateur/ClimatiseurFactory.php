<?php

namespace App\Domain\Refroidissement\Factory\Generateur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Refroidissement\Factory\GenerateurFactory;

final class ClimatiseurFactory extends GenerateurFactory
{
    public function set_reseau_froid(Id $reseau_froid_id): static
    {
        return $this;
    }

    public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool
    {
        return $type->is_climatiseur() && in_array($energie, [
            EnergieGenerateur::ELECTRICITE,
            EnergieGenerateur::GAZ_NATUREL,
            EnergieGenerateur::GPL,
        ]);
    }
}
