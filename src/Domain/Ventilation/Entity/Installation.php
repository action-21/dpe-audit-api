<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Ventilation\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

/**
 * TODO: Associer l'installation à un logement visité
 */
final class Installation
{
    private ?float $rdim = null;

    public function __construct(
        private readonly Id $id,
        private readonly Ventilation $ventilation,
        private float $surface,
        private SystemeCollection $systemes,
    ) {}

    public function controle(): void
    {
        Assert::greaterThan($this->surface, 0);
        $this->systemes->controle();
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

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->systemes->calcule_performance($moteur);
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

    public function ventilation(): Ventilation
    {
        return $this->ventilation;
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
        $this->reinitialise();
        return $this;
    }

    public function remove_systeme(Systeme $entity): self
    {
        $this->systemes->remove($entity);
        $this->reinitialise();
        return $this;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }
}
