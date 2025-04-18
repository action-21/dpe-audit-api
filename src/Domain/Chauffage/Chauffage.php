<?php

namespace App\Domain\Chauffage;

use App\Domain\Chauffage\Entity\{Emetteur, EmetteurCollection};
use App\Domain\Chauffage\Entity\{Generateur, GenerateurCollection};
use App\Domain\Chauffage\Entity\{Installation, InstallationCollection};
use App\Domain\Chauffage\Entity\{Systeme, SystemeCollection};
use App\Domain\Chauffage\Enum\TypeChauffage;
use App\Domain\Common\ValueObject\Id;
use Webmozart\Assert\Assert;

final class Chauffage
{
    public function __construct(
        private readonly Id $id,
        private GenerateurCollection $generateurs,
        private EmetteurCollection $emetteurs,
        private InstallationCollection $installations,
        private SystemeCollection $systemes,
        private ChauffageData $data,
    ) {}

    public static function create(): self
    {
        return new self(
            id: Id::create(),
            generateurs: new GenerateurCollection(),
            emetteurs: new EmetteurCollection(),
            installations: new InstallationCollection(),
            systemes: new SystemeCollection(),
            data: ChauffageData::create(),
        );
    }

    public function reinitialise(): self
    {
        $this->data = ChauffageData::create();
        $this->generateurs->reinitialise();
        $this->emetteurs->reinitialise();
        $this->installations->reinitialise();
        $this->systemes->reinitialise();
        return $this;
    }

    public function calcule(ChauffageData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function effet_joule(): bool
    {
        return $this->installations->effet_joule();
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
     * @return EmetteurCollection|Emetteur[]
     */
    public function emetteurs(): EmetteurCollection
    {
        return $this->emetteurs;
    }

    public function add_emetteur(Emetteur $entity): self
    {
        $this->emetteurs->add($entity);
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

    /**
     * On limite le nombre de systèmes centraux à 2 par installation
     */
    public function add_systeme(Systeme $entity): self
    {
        $installation = $this->installations->find($entity->installation()->id());
        $systemes = $installation->systemes()->with_type(TypeChauffage::CHAUFFAGE_CENTRAL);

        Assert::lessThan($systemes->count(), 2);

        $this->systemes->add($entity);
        $this->reinitialise();
        return $this;
    }

    public function data(): ChauffageData
    {
        return $this->data;
    }
}
