<?php

namespace App\Domain\Lnc;

use App\Domain\Audit\Audit;
use App\Domain\Audit\AuditTrait;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Lnc\Entity\{Baie, BaieCollection, Paroi, ParoiOpaqueCollection};
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Lnc\Service\{MoteurEnsoleillement, MoteurPerformance, MoteurSurfaceDeperditive};
use App\Domain\Lnc\ValueObject\{Ensoleillement, Performance, SurfaceDeperditive};

final class Lnc
{
    use AuditTrait;

    private ?SurfaceDeperditive $surface_deperditive = null;
    private ?Performance $performance = null;
    private ?Ensoleillement $ensoleillement = null;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypeLnc $type,
        private ParoiOpaqueCollection $parois,
        private BaieCollection $baies,
    ) {}

    public function controle(): void
    {
        $this->parois->controle();
        $this->baies->controle();
    }

    public function reinitialise(): void
    {
        $this->surface_deperditive = null;
        $this->ensoleillement = null;
        $this->parois->reinitialise();
        $this->baies->reinitialise();
    }

    public function calcule_surface_deperditive(MoteurSurfaceDeperditive $moteur): self
    {
        $this->surface_deperditive = $moteur($this);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur($this);
        return $this;
    }

    public function calcule_ensoleillement(MoteurEnsoleillement $moteur): self
    {
        $this->ensoleillement = $moteur($this);
        return $this;
    }

    public function audit(): Audit
    {
        return $this->enveloppe->audit();
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

    public function surface_deperditive(): SurfaceDeperditive
    {
        return $this->surface_deperditive;
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

    public function parois(): ParoiOpaqueCollection
    {
        return $this->parois;
    }

    public function add_paroi(Paroi $entity): self
    {
        $this->parois->add($entity);
        return $this;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function ensoleillement(): ?Ensoleillement
    {
        return $this->ensoleillement;
    }
}
