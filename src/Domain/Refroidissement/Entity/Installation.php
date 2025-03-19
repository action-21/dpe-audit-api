<?php

namespace App\Domain\Refroidissement\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Refroidissement\Service\{MoteurConsommation, MoteurDimensionnement};
use Webmozart\Assert\Assert;

/**
 * TODO: Associer l'installation à un logement visité
 */
final class Installation
{
    private ?float $rdim = null;

    public function __construct(
        private readonly Id $id,
        private readonly Refroidissement $refroidissement,
        private string $description,
        private float $surface,
        private SystemeCollection $systemes,
    ) {}

    public static function create(
        Id $id,
        Refroidissement $refroidissement,
        string $description,
        float $surface,
    ): Installation {
        Assert::greaterThan($surface, 0);

        return new Installation(
            id: $id,
            refroidissement: $refroidissement,
            description: $description,
            surface: $surface,
            systemes: new SystemeCollection(),
        );
    }

    public function controle(): void
    {
        Assert::greaterThan($this->surface, 0);
    }

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->systemes->reinitialise();
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->systemes->calcule_dimensionnement($moteur);
        $this->rdim = $moteur->calcule_dimensionnement_installation($this);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->systemes->calcule_consommations($moteur);
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

    public function surface(): float
    {
        return $this->surface;
    }

    public function systemes(): SystemeCollection
    {
        return $this->systemes;
    }

    public function add_systeme(Systeme $entity): self
    {
        $this->systemes->add($entity);
        return $this;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }
}
