<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Data\GenerateurData;
use App\Domain\Refroidissement\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Refroidissement\Factory\GenerateurFactory;

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private readonly ?ReseauFroid $reseau_froid,
        private string $description,
        private TypeGenerateur $type,
        private EnergieGenerateur $energie,
        private ?Annee $annee_installation,
        private ?float $seer,
        private GenerateurData $data,
    ) {}

    public static function create(GenerateurFactory $factory): self
    {
        return $factory->build();
    }

    public function reinitialise(): self
    {
        $this->data = GenerateurData::create();
        return $this;
    }

    public function calcule(GenerateurData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function refroidissement(): Refroidissement
    {
        return $this->refroidissement;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeGenerateur
    {
        return $this->type;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->energie;
    }

    public function annee_installation(): ?Annee
    {
        return $this->annee_installation;
    }

    public function seer(): ?float
    {
        return $this->seer;
    }

    public function reseau_froid(): ?ReseauFroid
    {
        return $this->reseau_froid;
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->refroidissement->systemes()->with_generateur($this->id);
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
