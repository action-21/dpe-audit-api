<?php

namespace App\Domain\Ecs\Engine\Rendement;

use App\Domain\Common\Enum\ScenarioUsage;
use App\Domain\Ecs\Entity\Systeme;
use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, TypeGenerateur};

final class RendementChauffeEauElectrique extends RendementSysteme
{
    public function rs(ScenarioUsage $scenario): float
    {
        $becs = $this->becs($scenario);
        $pertes = $this->pertes_stockage($scenario);
        $rd = $this->rd($scenario);

        if ($this->generateur()->type() === TypeGenerateur::CHAUFFE_EAU_VERTICAL) {
            if ($this->generateur()->signaletique()->label === LabelGenerateur::NE_PERFORMANCE_C) {
                return 1.08 / (1 + ($pertes * $rd) / ($becs * 1000));
            }
        }
        return 1 / (1 + ($pertes * $rd) / ($becs * 1000));
    }

    public function rg(ScenarioUsage $scenario): float
    {
        return $this->get("rg", function () {
            if (null === $rg = $this->table_repository->rg(
                type_generateur: $this->generateur()->type(),
                energie_generateur: $this->generateur()->energie(),
            )) {
                throw new \RuntimeException('Valeur forfaitaire Rg non trouvée');
            }
            return $rg;
        });
    }

    public static function supports(Systeme $systeme): bool
    {
        return in_array($systeme->generateur()->type(), [
            TypeGenerateur::CHAUFFE_EAU_INSTANTANE,
            TypeGenerateur::CHAUFFE_EAU_VERTICAL,
            TypeGenerateur::CHAUFFE_EAU_HORIZONTAL,
            TypeGenerateur::CHAUDIERE,
        ]) && $systeme->generateur()->energie() === EnergieGenerateur::ELECTRICITE;
    }
}
