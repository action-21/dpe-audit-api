<?php

namespace App\Domain\Porte;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Entity\Paroi;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use App\Domain\Porte\Enum\{EtatIsolation, Materiau, Mitoyennete, TypePose};
use App\Domain\Porte\Service\MoteurPerformance;
use App\Domain\Porte\ValueObject\{Menuiserie, Performance, Position, Vitrage};
use Webmozart\Assert\Assert;

final class Porte extends Paroi
{
    private ?Performance $performance = null;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private TypePose $type_pose,
        private ?EtatIsolation $isolation,
        private ?Materiau $materiau,
        private bool $presence_sas,
        private ?int $annee_installation,
        private ?float $u,
        private Position $position,
        private Menuiserie $menuiserie,
        private Vitrage $vitrage,
    ) {}

    public function controle(): void
    {
        Assert::nullOrGreaterThanEq($this->annee_installation, $this->annee_construction_batiment());

        if ($this->position->paroi_id) {
            Assert::notNull($this->paroi());
            Assert::eq($this->position->mitoyennete, $this->paroi()->mitoyennete());
            Assert::eq($this->position->orientation, $this->paroi()->orientation());
        }
        if ($this->position->local_non_chauffe_id) {
            Assert::notNull($this->local_non_chauffe());
        }
    }

    public function reinitialise(): void
    {
        $this->performance = null;
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur($this);
        return $this;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function type_pose(): TypePose
    {
        return $this->type_pose;
    }

    public function isolation(?bool $defaut = false): ?EtatIsolation
    {
        return $defaut ? ($this->isolation ?? MoteurPerformance::isolation_defaut()) : $this->isolation;
    }

    public function materiau(?bool $defaut = false): ?Materiau
    {
        return $defaut ? ($this->materiau ?? MoteurPerformance::materiau_defaut()) : $this->materiau;
    }

    public function presence_sas(): bool
    {
        return $this->presence_sas;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function u(): ?float
    {
        return $this->u;
    }

    public function position(): Position
    {
        return $this->position;
    }

    public function menuiserie(): Menuiserie
    {
        return $this->menuiserie;
    }

    public function vitrage(): Vitrage
    {
        return $this->vitrage;
    }

    /**
     * @inheritdoc
     */
    public function paroi(): ?Paroi
    {
        return $this->position->paroi_id ? $this->enveloppe->parois()->get(id: $this->position->paroi_id) : null;
    }

    /**
     * @inheritdoc
     */
    public function local_non_chauffe(): ?Lnc
    {
        if ($this->paroi()) {
            return $this->paroi()->local_non_chauffe();
        }
        return $this->position->local_non_chauffe_id
            ? $this->enveloppe->locaux_non_chauffes()->find(id: $this->position->local_non_chauffe_id)
            : null;
    }

    /**
     * @inheritdoc
     */
    public function mitoyennete(): Mitoyennete
    {
        return $this->position->mitoyennete;
    }

    /**
     * @inheritdoc
     */
    public function orientation(): ?float
    {
        return $this->position->orientation;
    }

    /**
     * @inheritdoc
     */
    public function b(): ?float
    {
        return $this->performance?->b;
    }
}
