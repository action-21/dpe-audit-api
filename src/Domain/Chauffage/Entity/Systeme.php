<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Data\SystemeData;
use App\Domain\Chauffage\Enum\TypeChauffage;
use App\Domain\Chauffage\ValueObject\Reseau;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class Systeme
{
    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private readonly Installation $installation,
        private readonly Generateur $generateur,
        private TypeChauffage $type_chauffage,
        private ?Reseau $reseau,
        private EmetteurCollection $emetteurs,
        private SystemeData $data,
    ) {}

    public static function create(
        Id $id,
        Chauffage $chauffage,
        Installation $installation,
        Generateur $generateur,
        ?Reseau $reseau,
    ): self {
        if (false === $generateur->type()->is_chauffage_divise()) {
            Assert::notNull($reseau);
        } else {
            $reseau = null;
        }

        return new self(
            id: $id,
            chauffage: $chauffage,
            installation: $installation,
            generateur: $generateur,
            type_chauffage: $reseau ? TypeChauffage::CHAUFFAGE_CENTRAL : TypeChauffage::CHAUFFAGE_DIVISE,
            reseau: $generateur->type()->is_chauffage_central() ? $reseau : null,
            emetteurs: new EmetteurCollection(),
            data: SystemeData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = SystemeData::create();
        return $this;
    }

    public function calcule(SystemeData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function generateur(): Generateur
    {
        return $this->generateur;
    }

    public function effet_joule(): bool
    {
        return $this->generateur->effet_joule();
    }

    public function type_chauffage(): TypeChauffage
    {
        return $this->type_chauffage;
    }

    public function reseau(): ?Reseau
    {
        return $this->reseau;
    }

    /**
     * @return EmetteurCollection|Emetteur[]
     */
    public function emetteurs(): EmetteurCollection
    {
        return $this->emetteurs;
    }

    public function reference_emetteur(Emetteur $entity): self
    {
        if ($this->type_chauffage() === TypeChauffage::CHAUFFAGE_CENTRAL) {
            $this->emetteurs->add($entity);
            $this->reinitialise();
        }
        return $this;
    }

    public function data(): SystemeData
    {
        return $this->data;
    }
}
