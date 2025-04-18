<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Ventilation\Data\GenerateurData;
use App\Domain\Ventilation\Enum\{TypeGenerateur, TypeVmc};
use App\Domain\Ventilation\Ventilation;

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private string $description,
        private TypeGenerateur $type,
        private bool $presence_echangeur_thermique,
        private bool $generateur_collectif,
        private ?Annee $annee_installation,
        private ?TypeVmc $type_vmc,
        private GenerateurData $data,
    ) {}

    public static function create(
        Id $id,
        Ventilation $ventilation,
        string $description,
        TypeGenerateur $type,
        bool $presence_echangeur_thermique,
        bool $generateur_collectif,
        ?Annee $annee_installation,
        ?TypeVmc $type_vmc,
    ): self {
        return new self(
            id: $id,
            ventilation: $ventilation,
            description: $description,
            type: $type,
            presence_echangeur_thermique: $presence_echangeur_thermique,
            generateur_collectif: $type->is_generateur_collectif() ?? $generateur_collectif,
            annee_installation: $annee_installation,
            type_vmc: $type->is_vmc() ? $type_vmc : null,
            data: GenerateurData::create(),
        );
    }

    public function calcule(GenerateurData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->data = GenerateurData::create();
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeGenerateur
    {
        return $this->type;
    }

    public function generateur_collectif(): bool
    {
        return $this->generateur_collectif;
    }

    public function presence_echangeur_thermique(): bool
    {
        return $this->presence_echangeur_thermique;
    }

    public function annee_installation(): ?Annee
    {
        return $this->annee_installation;
    }

    public function type_vmc(): ?TypeVmc
    {
        return $this->type_vmc;
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->ventilation->systemes()->with_generateur($this->id);
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
