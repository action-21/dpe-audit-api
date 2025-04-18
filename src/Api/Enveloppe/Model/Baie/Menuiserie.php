<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie as Entity;
use App\Domain\Enveloppe\ValueObject\Baie\Menuiserie as Value;
use Symfony\Component\Validator\Constraints as Assert;

final class Menuiserie
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public readonly ?float $largeur_dormant,

        public readonly ?bool $presence_joint,

        public readonly ?bool $presence_retour_isolation,

        public readonly ?bool $presence_rupteur_pont_thermique,
    ) {}

    public static function from(Entity $entity): ?self
    {
        return ($vo = $entity->menuiserie()) ? self::from_value($vo) : null;
    }

    public static function from_value(Value $value): self
    {
        return new self(
            largeur_dormant: $value->largeur_dormant,
            presence_joint: $value->presence_joint,
            presence_retour_isolation: $value->presence_retour_isolation,
            presence_rupteur_pont_thermique: $value->presence_rupteur_pont_thermique,
        );
    }
}
