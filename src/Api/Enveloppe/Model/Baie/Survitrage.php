<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie as Entity;
use App\Domain\Enveloppe\Enum\Baie\TypeSurvitrage;
use App\Domain\Enveloppe\ValueObject\Baie\Survitrage as Value;
use Symfony\Component\Validator\Constraints as Assert;

final class Survitrage
{
    public function __construct(
        public readonly ?TypeSurvitrage $type_survitrage,

        #[Assert\PositiveOrZero]
        public readonly ?float $epaisseur_lame,
    ) {}

    public static function from(Entity $entity): ?self
    {
        return ($vo = $entity->vitrage()?->survitrage) ? self::from_value($vo) : null;
    }

    public static function from_value(Value $value): self
    {
        return new self(
            type_survitrage: $value->type_survitrage,
            epaisseur_lame: $value->epaisseur_lame,
        );
    }
}
