<?php

namespace App\Domain\Ventilation\Entity;

use App\Domain\Common\Type\Id;
use App\Domain\Common\ValueObject\ConsommationCollection;
use App\Domain\Ventilation\Enum\{ModeExtraction, ModeInsufflation, TypeSysteme, TypeVentilation};
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
        private TypeSysteme $type,
        private ?Generateur $generateur,
        private ?ModeExtraction $mode_extraction,
        private ?ModeInsufflation $mode_insufflation,
    ) {}

    public function set_ventilation_naturelle(
        ?ModeExtraction $mode_extraction,
        ?ModeInsufflation $mode_insufflation,
    ): self {
        $this->mode_extraction = $mode_extraction;
        $this->mode_insufflation = $mode_insufflation;
        $this->type = TypeSysteme::VENTILATION_NATURELLE;
        $this->generateur = null;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function set_ventilation_centralisee(
        Generateur $generateur,
        ModeExtraction $mode_extraction,
        ModeInsufflation $mode_insufflation,
    ): self {
        Assert::same($generateur->type_ventilation(), TypeVentilation::VENTILATION_MECANIQUE_CENTRALISEE);

        $this->generateur = $generateur;
        $this->type = TypeSysteme::from_type_generateur($generateur->type());
        $this->mode_extraction = $mode_extraction;
        $this->mode_insufflation = $mode_insufflation;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function set_ventilation_divisee(Generateur $generateur): self
    {
        Assert::same($generateur->type_ventilation(), TypeVentilation::VENTILATION_MECANIQUE_DIVISEE);

        $this->generateur = $generateur;
        $this->type = TypeSysteme::from_type_generateur($generateur->type());
        $this->mode_extraction = null;
        $this->mode_insufflation = null;

        $this->controle();
        $this->reinitialise();
        return $this;
    }

    public function reinitialise(): void
    {
        $this->rdim = null;
        $this->performance = null;
        $this->consommations = null;
    }

    public function controle(): void {}

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

    public function generateur(): ?Generateur
    {
        return $this->generateur;
    }

    public function type(): TypeSysteme
    {
        return $this->type;
    }

    public function mode_extraction(): ?ModeExtraction
    {
        return $this->mode_extraction;
    }

    public function mode_insufflation(): ?ModeInsufflation
    {
        return $this->mode_insufflation;
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
