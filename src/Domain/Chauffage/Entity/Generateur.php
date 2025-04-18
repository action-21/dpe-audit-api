<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Data\GenerateurData;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur, UsageChauffage};
use App\Domain\Chauffage\Factory\GenerateurFactory;
use App\Domain\Chauffage\ValueObject\Generateur\{Combustion, Position, Signaletique};
use App\Domain\Common\ValueObject\{Annee, Id};

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private string $description,
        private TypeGenerateur $type,
        private EnergieGenerateur $energie,
        private ?EnergieGenerateur $energie_partie_chaudiere,
        private ?Annee $annee_installation,
        private UsageChauffage $usage,
        private Position $position,
        private Signaletique $signaletique,
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

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
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

    public function energie_partie_chaudiere(): ?EnergieGenerateur
    {
        return $this->energie_partie_chaudiere;
    }

    public function usage(): UsageChauffage
    {
        return $this->usage;
    }

    public function annee_installation(): ?Annee
    {
        return $this->annee_installation;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }

    public function combustion(): ?Combustion
    {
        return $this->signaletique->combustion;
    }

    public function effet_joule(): bool
    {
        return $this->energie === EnergieGenerateur::ELECTRICITE;
    }

    /**
     * @return InstallationCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->chauffage->installations()->with_generateur($this->id());
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->chauffage->systemes()->with_generateur($this->id());
    }

    /**
     * @return EmetteurCollection|Emetteur[]
     */
    public function emetteurs(): EmetteurCollection
    {
        return $this->chauffage->emetteurs()->with_generateur($this->id());
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
