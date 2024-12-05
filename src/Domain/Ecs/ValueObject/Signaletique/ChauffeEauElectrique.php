<?php

namespace App\Domain\Ecs\ValueObject\Signaletique;

use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;
use Webmozart\Assert\Assert;

final class ChauffeEauElectrique extends Signaletique
{
    public static function create(
        TypeGenerateur\ChauffeEauElectrique $type,
        int $volume_stockage,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        LabelGenerateur $label,
        ?float $pn,
    ): static {
        if (\in_array($type, [
            TypeGenerateur\ChauffeEauElectrique::BALLON_ELECTRIQUE_HORIZONTAL,
            TypeGenerateur\ChauffeEauElectrique::BALLON_ELECTRIQUE_VERTICAL
        ])) {
            Assert::greaterThan($volume_stockage, 0);
        }
        if ($type === TypeGenerateur\ChauffeEauElectrique::CHAUFFE_EAU_INSTANTANE) {
            Assert::eq($volume_stockage, 0);
        }

        $value = new static(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            label: $label,
            pn: $pn,
        );
        $value->controle();
        return $value;
    }
}
