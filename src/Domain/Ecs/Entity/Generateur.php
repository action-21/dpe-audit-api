<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Ecs\Data\GenerateurData;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, UsageEcs};
use App\Domain\Ecs\Factory\GenerateurFactory;
use App\Domain\Ecs\ValueObject\Generateur\{Combustion, Position, Signaletique};

final class Generateur
{
    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private string $description,
        private TypeGenerateur $type,
        private EnergieGenerateur $energie,
        private UsageEcs $usage,
        private ?Annee $annee_installation,
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

    public function ecs(): Ecs
    {
        return $this->ecs;
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

    public function usage(): UsageEcs
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

    /**
     * @return InstallationCollection|Installation[]
     */
    public function installations(): InstallationCollection
    {
        return $this->ecs->installations()->with_generateur($this->id);
    }

    /**
     * @return SystemeCollection|Systeme[]
     */
    public function systemes(): SystemeCollection
    {
        return $this->ecs->systemes()->with_generateur($this->id);
    }

    public function data(): GenerateurData
    {
        return $this->data;
    }
}
