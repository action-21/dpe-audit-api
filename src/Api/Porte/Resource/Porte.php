<?php

namespace App\Api\Resource;

use App\Domain\Porte\Enum\{TypePorte, TypePose};
use App\Domain\Porte\{Porte as Entity, PorteEngine};

final class Porte
{
    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly TypePorte $type_porte,
        public readonly TypePose $type_pose,
        public readonly float $surface,
        public readonly bool $presence_joint,
        public readonly ?bool $presence_retour_isolation = null,
        public readonly ?float $uporte = null,
        public readonly ?float $largeur_dormant = null,
        public readonly null|false|float $dp = null,
        public readonly null|false|float $b = null,
        public readonly null|false|float $sdep = null,
        public readonly null|false|float $u = null,
    ) {
    }

    public static function from(Entity $entity, ?PorteEngine $engine = null): self
    {
        return new self(
            id: (string) $entity->id(),
            description: $entity->description(),
            type_porte: $entity->caracteristique()->type_porte,
            type_pose: $entity->caracteristique()->type_pose,
            surface: $entity->caracteristique()->surface->valeur(),
            presence_joint: $entity->caracteristique()->presence_joint,
            presence_retour_isolation: $entity->caracteristique()->presence_retour_isolation,
            uporte: $entity->caracteristique()->uporte?->valeur(),
            largeur_dormant: $entity->caracteristique()->largeur_dormant?->valeur(),
            dp: $engine?->deperdition()->dp(),
            b: $engine?->deperdition()->b(),
            sdep: $engine?->deperdition()->sdep(),
            u: $engine?->deperdition()->u(),
        );
    }
}
