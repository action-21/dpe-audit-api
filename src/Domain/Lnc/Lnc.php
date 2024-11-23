<?php

namespace App\Domain\Lnc;

use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Common\Enum\ZoneClimatique;
use App\Domain\Common\Type\Id;
use App\Domain\Lnc\Entity\{Baie, BaieCollection, Paroi, ParoiCollection};
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Lnc\Service\{MoteurEnsoleillement, MoteurPerformance, MoteurSurfaceDeperditive};
use App\Domain\Lnc\ValueObject\{EnsoleillementCollection, Performance};

final class Lnc
{
    private ?float $aiu = null;
    private ?float $aue = null;
    private ?bool $isolation_aiu = null;
    private ?bool $isolation_aue = null;
    private ?Performance $performance = null;
    private ?EnsoleillementCollection $ensoleillement = null;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeLnc $type,
        private ParoiCollection $parois,
        private BaieCollection $baies,
    ) {}

    public function update(string $description, TypeLnc $type): self
    {
        $this->description = $description;
        $this->type = $type;
        return $this;
    }

    public function controle(): void
    {
        $this->parois->controle();
        $this->baies->controle();
    }

    public function reinitialise(): void
    {
        $this->aiu = null;
        $this->aue = null;
        $this->isolation_aiu = null;
        $this->isolation_aue = null;
        $this->performance = null;
        $this->ensoleillement = null;
        $this->parois->reinitialise();
        $this->baies->reinitialise();
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        $this->parois->calcule_surface_deperditive($moteur);
        $this->aiu = $moteur->calcule_aiu($this);
        $this->aue = $moteur->calcule_aue($this);
        $this->isolation_aiu = $moteur->calcule_isolation_aiu($this);
        $this->isolation_aue = $moteur->calcule_isolation_aue($this);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur->calcule_performance($this);
        return $this;
    }

    public function calcule_ensoleillement(MoteurEnsoleillement $moteur): self
    {
        $this->baies->calcule_ensoleillement($moteur);
        $this->ensoleillement = $moteur->calcule_ensoleillement($this);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type(): TypeLnc
    {
        return $this->type;
    }

    public function est_ets(): bool
    {
        return $this->type === TypeLnc::ESPACE_TAMPON_SOLARISE;
    }

    public function est_combles(): bool
    {
        return \in_array($this->type, [
            TypeLnc::COMBLE_FORTEMENT_VENTILE,
            TypeLnc::COMBLE_FAIBLEMENT_VENTILE,
            TypeLnc::COMBLE_TRES_FAIBLEMENT_VENTILE
        ]);
    }

    public function baies(): BaieCollection
    {
        return $this->baies;
    }

    public function add_baie(Baie $entity): self
    {
        $this->baies->add($entity);
        return $this;
    }

    public function parois(): ParoiCollection
    {
        return $this->parois;
    }

    public function add_paroi(Paroi $entity): self
    {
        $this->parois->add($entity);
        return $this;
    }

    public function zone_climatique(): ZoneClimatique
    {
        return $this->enveloppe->audit()->zone_climatique();
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function ensoleillement(): ?EnsoleillementCollection
    {
        return $this->ensoleillement;
    }

    public function aue(): ?float
    {
        return $this->aue;
    }

    public function aiu(): ?float
    {
        return $this->aiu;
    }

    public function isolation_aue(): ?bool
    {
        return $this->isolation_aue;
    }

    public function isolation_aiu(): ?bool
    {
        return $this->isolation_aiu;
    }
}
