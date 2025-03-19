<?php

namespace App\Domain\Lnc\Service;

use App\Domain\Common\Enum\Mois;
use App\Domain\Lnc\Lnc;
use App\Domain\Lnc\ValueObject\{Ensoleillement, EnsoleillementItem};

final class MoteurEnsoleillement
{
    public function __construct(
        private MoteurEnsoleillementBaie $moteur_ensoleillement_baie,
    ) {}

    public function __invoke(Lnc $entity): Ensoleillement
    {
        $entity->baies()->calcule_ensoleillement($this->moteur_ensoleillement_baie);

        return Ensoleillement::create(function (Mois $mois) use ($entity) {
            return EnsoleillementItem::create(
                mois: $mois,
                t: $entity->baies()->t($mois),
                sst: $entity->baies()->sst($mois),
            );
        });
    }
}
