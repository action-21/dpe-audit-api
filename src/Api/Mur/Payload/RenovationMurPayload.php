<?php

namespace App\Api\Mur\Payload;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Mur\Enum\TypeIsolation;
use App\Domain\Mur\ValueObject\Isolation;
use Symfony\Component\Validator\Constraints as Assert;

final class RenovationMurPayload
{
    public function __construct(
        public string $id,
        public TypeIsolation $type_isolation,
        #[Assert\Positive]
        public ?int $epaisseur_isolation,
        #[Assert\Positive]
        public ?float $resistance_thermique_isolation,
    ) {}

    public function id(): Id
    {
        return Id::from($this->id);
    }

    public function to(): Isolation
    {
        return Isolation::create_isole(
            type_isolation: $this->type_isolation,
            annee_isolation: (int) (new \DateTime())->format('Y'),
            epaisseur_isolation: $this->epaisseur_isolation,
            resistance_thermique_isolation: $this->resistance_thermique_isolation,
        );
    }
}
