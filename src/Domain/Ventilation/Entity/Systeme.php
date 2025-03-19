<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ventilation\Enum\TypeVentilation;
use App\Domain\Ventilation\Service\{MoteurConsommation, MoteurDimensionnement, MoteurPerformance};
use App\Domain\Ventilation\ValueObject\Performance;
use App\Domain\Ventilation\Ventilation;
use Webmozart\Assert\Assert;

final class Systeme
{
    private ?float $rdim = null;
    private ?Performance $performance = null;
    private ?ConsommationCollection $consommations = null;

    public function __construct(
        private readonly Id $id,
        private readonly Installation $installation,
        private TypeVentilation $type_ventilation,
        private ?Generateur $generateur,
    ) {}

    public static function create(
        Id $id,
        Installation $installation,
        TypeVentilation $type_ventilation,
        ?Generateur $generateur,
    ): Systeme {
        if ($type_ventilation === TypeVentilation::VENTILATION_MECANIQUE) {
            Assert::notNull($generateur);
        }
        return new Systeme(
            id: $id,
            installation: $installation,
            type_ventilation: $type_ventilation,
            generateur: $type_ventilation === TypeVentilation::VENTILATION_MECANIQUE ? $generateur : null,
        );
    }

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->performance = null;
        $this->consommations = null;
    }

    public function controle(): void
    {
        if ($this->type_ventilation === TypeVentilation::VENTILATION_MECANIQUE) {
            Assert::notNull($this->generateur);
        }
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur): self
    {
        $this->rdim = $moteur->calcule_dimensionnement_systeme($this);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur->calcule_performance_systeme($this);
        return $this;
    }

    public function calcule_consommations(MoteurConsommation $moteur): self
    {
        $this->consommations = $moteur->calcule_consommations($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ventilation(): Ventilation
    {
        return $this->installation->ventilation();
    }

    public function installation(): Installation
    {
        return $this->installation;
    }

    public function type_ventilation(): TypeVentilation
    {
        return $this->type_ventilation;
    }

    public function generateur(): ?Generateur
    {
        return $this->generateur;
    }

    public function rdim(): ?float
    {
        return $this->rdim;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function consommations(): ?ConsommationCollection
    {
        return $this->consommations;
    }
}
