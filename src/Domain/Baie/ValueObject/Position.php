<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\Mitoyennete;
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;

final class Position
{
    public function __construct(
        public readonly ?Id $paroi_id = null,
        public readonly ?Id $local_non_chauffe_id = null,
        public readonly ?Mitoyennete $mitoyennete = null,
        public readonly ?float $orientation = null,
    ) {}

    public static function create(Mitoyennete $mitoyennete, ?float $orientation,): self
    {
        return new self(mitoyennete: $mitoyennete, orientation: $orientation);
    }

    public static function create_liaison_paroi(Id $paroi_id): self
    {
        return new self(paroi_id: $paroi_id);
    }

    public static function create_liaison_local_non_chauffe(Id $local_non_chauffe_id, ?float $orientation): self
    {
        return new self(
            local_non_chauffe_id: $local_non_chauffe_id,
            mitoyennete: Mitoyennete::LOCAL_NON_CHAUFFE,
            orientation: $orientation,
        );
    }

    public function controle(): void
    {
        Assert::orientation($this->orientation);

        if ($this->mitoyennete->local_non_chauffe() && $this->local_non_chauffe_id === null)
            throw new \DomainException('Référence au local non chauffé manquante');
    }
}
