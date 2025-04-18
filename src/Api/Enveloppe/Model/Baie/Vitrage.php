<?php

namespace App\Api\Enveloppe\Model\Baie;

use App\Domain\Enveloppe\Entity\Baie as Entity;
use App\Domain\Enveloppe\Enum\Baie\{NatureGazLame, TypeVitrage};
use App\Domain\Enveloppe\ValueObject\Baie\Vitrage as Value;
use Symfony\Component\Validator\Constraints as Assert;

final class Vitrage
{
    public function __construct(
        public readonly ?TypeVitrage $type_vitrage,

        public readonly ?NatureGazLame $nature_lame,

        #[Assert\PositiveOrZero]
        public readonly ?float $epaisseur_lame,

        #[Assert\Valid]
        public readonly ?Survitrage $survitrage,
    ) {}

    public static function from(Entity $entity): ?self
    {
        return ($vo = $entity->vitrage()) ? self::from_value($vo) : null;
    }

    public static function from_value(Value $value): self
    {
        return new self(
            type_vitrage: $value->type_vitrage,
            nature_lame: $value->nature_gaz_lame,
            epaisseur_lame: $value->epaisseur_lame,
            survitrage: $value->survitrage ? Survitrage::from_value($value->survitrage) : null,
        );
    }
}
