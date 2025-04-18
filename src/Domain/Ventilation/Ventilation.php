<?php

namespace App\Domain\Ventilation;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Entity\{Generateur, GenerateurCollection};
use App\Domain\Ventilation\Entity\{Installation, InstallationCollection};
use App\Domain\Ventilation\Entity\{Systeme, SystemeCollection};

final class Ventilation
{
    public function __construct(
        private readonly Id $id,
        private GenerateurCollection $generateurs,
        private InstallationCollection $installations,
        private SystemeCollection $systemes,
        private VentilationData $data,
    ) {}

    public static function create(): self
    {
        return new self(
            id: Id::create(),
            generateurs: new GenerateurCollection(),
            installations: new InstallationCollection(),
            systemes: new SystemeCollection(),
            data: VentilationData::create(),
        );
    }

    public function calcule(VentilationData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): void
    {
        $this->installations->reinitialise();
        $this->generateurs->reinitialise();
        $this->systemes->reinitialise();
    }

    public function id(): Id
    {
        return $this->id;
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

    public function data(): VentilationData
    {
        return $this->data;
    }
}
