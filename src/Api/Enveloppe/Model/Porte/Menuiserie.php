<?php

namespace App\Api\Enveloppe\Model\Porte;

use App\Domain\Enveloppe\Entity\Porte as Entity;
use Symfony\Component\Validator\Constraints as Assert;

final class Menuiserie
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public readonly ?float $largeur_dormant,

        public readonly ?bool $presence_joint,

        public readonly ?bool $presence_retour_isolation,
    ) {}

    public static function from(Entity $entity): self
    {
        return new self(
            largeur_dormant: $entity->menuiserie()->largeur_dormant,
            presence_joint: $entity->menuiserie()->presence_joint,
            presence_retour_isolation: $entity->menuiserie()->presence_retour_isolation,
        );
    }
}
