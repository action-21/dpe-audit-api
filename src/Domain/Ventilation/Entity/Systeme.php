<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Data\SystemeData;
use App\Domain\Ventilation\Enum\TypeVentilation;
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

final class Systeme
{
    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private readonly Installation $installation,
        private TypeVentilation $type,
        private ?Generateur $generateur,
        private SystemeData $data,
    ) {}

    public static function create(
        Id $id,
        Ventilation $ventilation,
        Installation $installation,
        TypeVentilation $type,
        ?Generateur $generateur,
    ): self {
        if ($type === TypeVentilation::VENTILATION_MECANIQUE) {
            Assert::notNull($generateur);
        } else {
            $generateur = null;
        }
        return new self(
            id: $id,
            ventilation: $ventilation,
            installation: $installation,
            type: $type,
            generateur: $generateur,
            data: SystemeData::create(),
        );
    }

    public function calcule(SystemeData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->data = SystemeData::create();
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function type(): TypeVentilation
    {
        return $this->type;
    }

    public function generateur(): ?Generateur
    {
        return $this->generateur;
    }

    public function data(): SystemeData
    {
        return $this->data;
    }
}
