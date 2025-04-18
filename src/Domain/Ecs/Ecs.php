<?php

namespace App\Domain\Ecs;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ecs\Entity\{Generateur, GenerateurCollection};
use App\Domain\Ecs\Entity\{Installation, InstallationCollection};
use App\Domain\Ecs\Entity\{Systeme, SystemeCollection};

final class Ecs
{
    public function __construct(
        private readonly Id $id,
        private InstallationCollection $installations,
        private GenerateurCollection $generateurs,
        private SystemeCollection $systemes,
        private EcsData $data,
    ) {}

    public static function create(): self
    {
        return new self(
            id: Id::create(),
            generateurs: new GenerateurCollection(),
            installations: new InstallationCollection(),
            systemes: new SystemeCollection(),
            data: EcsData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = EcsData::create();
        $this->generateurs->reinitialise();
        $this->installations->reinitialise();
        $this->systemes->reinitialise();
        return $this;
    }

    public function calcule(EcsData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    /**
     * @return InstallationCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->installations;
    }

    public function add_installation(Installation $entity): self
    {
        $this->installations->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return GenerateurCollection|Generateur[]
     */
    public function generateurs(): GenerateurCollection
    {
        return $this->generateurs;
    }

    public function add_generateur(Generateur $entity): self
    {
        $this->generateurs->add($entity);
        $this->reinitialise();
        return $this;
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->systemes;
    }

    public function add_systeme(Systeme $entity): self
    {
        $this->systemes->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function data(): EcsData
    {
        return $this->data;
    }
}
