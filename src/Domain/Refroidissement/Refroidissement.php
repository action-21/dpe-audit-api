<?php

namespace App\Domain\Refroidissement;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Entity\{Generateur, GenerateurCollection};
use App\Domain\Refroidissement\Entity\{Installation, InstallationCollection};
use App\Domain\Refroidissement\Entity\{Systeme, SystemeCollection};

final class Refroidissement
{
    public function __construct(
        private readonly Id $id,
        private GenerateurCollection $generateurs,
        private InstallationCollection $installations,
        private SystemeCollection $systemes,
        private RefroidissementData $data,
    ) {}

    public static function create(): self
    {
        return new self(
            id: Id::create(),
            generateurs: new GenerateurCollection(),
            installations: new InstallationCollection(),
            systemes: new SystemeCollection(),
            data: RefroidissementData::create(),
        );
    }

    public function calcule(RefroidissementData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function reinitialise(): self
    {
        $this->data = RefroidissementData::create();
        $this->generateurs->reinitialise();
        $this->installations->reinitialise();
        $this->systemes->reinitialise();
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

    public function data(): RefroidissementData
    {
        return $this->data;
    }
}
