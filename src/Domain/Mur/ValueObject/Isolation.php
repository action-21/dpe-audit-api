<?php

namespace App\Domain\Mur\ValueObject;

use App\Domain\Mur\Enum\TypeIsolation;

final class Isolation
{
    public function __construct(
        public readonly TypeIsolation $type_isolation,
        public readonly ?AnneeIsolation $annnee_isolation = null,
        public readonly ?EpaisseurIsolant $epaisseur_isolant = null,
        public readonly ?ResistanceIsolant $resistance_thermique = null,
    ) {
    }

    public static function create(
        TypeIsolation $type_isolation,
        ?AnneeIsolation $annnee_isolation = null,
        ?EpaisseurIsolant $epaisseur_isolant = null,
        ?ResistanceIsolant $resistance_thermique = null,
    ): self {
        if ($type_isolation === TypeIsolation::INCONNU) {
            return new self(type_isolation: TypeIsolation::INCONNU);
        }
        if ($type_isolation === TypeIsolation::NON_ISOLE) {
            return new self(type_isolation: TypeIsolation::NON_ISOLE);
        }
        return new self(
            type_isolation: $type_isolation,
            annnee_isolation: $annnee_isolation,
            epaisseur_isolant: $epaisseur_isolant,
            resistance_thermique: $resistance_thermique,
        );
    }
}
