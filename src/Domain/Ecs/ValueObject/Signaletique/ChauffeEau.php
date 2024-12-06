<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\{Combustion, Signaletique};

final class ChauffeEau extends Signaletique
{
    public static function create(
        TypeGenerateur\ChauffeEau $type,
        EnergieGenerateur\ChauffeEau $energie,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?float $pn,
        ?LabelGenerateur $label,
        ?Combustion $combustion,
    ): static {
        $type = $type->to();
        $energie = $energie->to();

        if ($volume_stockage === 0) {
            $type = TypeGenerateur::CHAUFFE_EAU_INSTANTANE;
        } elseif ($type === TypeGenerateur::CHAUFFE_EAU_INSTANTANE) {
            $type = $energie === EnergieGenerateur::ELECTRICITE
                ? TypeGenerateur::CHAUFFE_EAU_VERTICAL
                : TypeGenerateur::ACCUMULATEUR;
        }

        if ($energie === EnergieGenerateur::ELECTRICITE) {
            $combustion = null;
            $label = $label ?? LabelGenerateur::INCONNU;
        } else {
            $combustion = $combustion ?? Combustion::default();
            $label = null;
        }

        $value = new static(
            type: $type,
            energie: $energie,
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            pn: $pn,
            label: $label,
            combustion: $combustion,
        );
        $value->controle();
        return $value;
    }
}
