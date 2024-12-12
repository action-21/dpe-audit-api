<?php

namespace App\Api\Ventilation\Payload;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Enum\TypeVentilation;
use Symfony\Component\Validator\Constraints as Assert;

final class SystemePayload
{
    public function __construct(
        #[Assert\Uuid]
        public string $id,
        #[Assert\Uuid]
        public ?string $generateur_id,

        public TypeVentilation $type_ventilation,
    ) {}

    public function id(): Id
    {
        return Id::from($this->id);
    }

    public function generateur_id(): ?Id
    {
        return $this->generateur_id ? Id::from($this->generateur_id) : null;
    }

    #[Assert\IsTrue]
    public function isValid(): bool
    {
        return $this->type_ventilation !== TypeVentilation::VENTILATION_MECANIQUE || $this->generateur_id;
    }

    public function type_ventilation_naturelle(): ?TypeVentilation\VentilationNaturelle
    {
        return $this->type_ventilation !== TypeVentilation::VENTILATION_MECANIQUE
            ? TypeVentilation\VentilationNaturelle::from($this->type_ventilation->value)
            : null;
    }
}
