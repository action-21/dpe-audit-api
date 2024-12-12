<?php

namespace App\Domain\PontThermique;

use App\Domain\Baie\Baie;
use App\Domain\Common\Enum\Enum;
use App\Domain\Common\Type\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Mur\Mur;
use App\Domain\PlancherBas\PlancherBas;
use App\Domain\PlancherHaut\PlancherHaut;
use App\Domain\PontThermique\Enum\{TypeIsolation, TypeLiaison, TypePose};
use App\Domain\PontThermique\Service\MoteurPerformance;
use App\Domain\PontThermique\ValueObject\{Liaison, Performance};
use App\Domain\Porte\Porte;
use Webmozart\Assert\Assert;

final class PontThermique
{
    private ?Performance $performance = null;

    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private float $longueur,
        private Liaison $liaison,
        private ?float $kpt = null,
    ) {}

    public static function create(
        Id $id,
        Enveloppe $enveloppe,
        string $description,
        float $longueur,
        Liaison $liaison,
        ?float $kpt = null,
    ): self {
        Assert::greaterThan($longueur, 0);
        Assert::nullOrGreaterThan($kpt, 0);

        return new self(
            id: $id,
            enveloppe: $enveloppe,
            description: $description,
            longueur: $longueur,
            liaison: $liaison,
            kpt: $kpt,
        );
    }

    public function reinitialise(): void
    {
        $this->performance = null;
    }

    public function controle(): void
    {
        Assert::greaterThan($this->longueur, 0);
        Assert::nullOrGreaterThan($this->kpt, 0);
    }

    public function calcule_performance(MoteurPerformance $moteur): self
    {
        $this->performance = $moteur->calcule_performance($this);
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

    public function mur(): Mur
    {
        return $this->enveloppe->parois()->murs()->find(id: $this->liaison->mur_id);
    }

    /**
     * TODO: Définir l'état d'isolation par défaut des planchers bas à isolation répartie
     */
    public function type_isolation_mur(): TypeIsolation
    {
        if (false === $this->mur()->est_isole())
            return TypeIsolation::NON_ISOLE;
        if (false === $this->mur()->isolation()->type_isolation->inconnu())
            return TypeIsolation::from($this->mur()->isolation()->type_isolation->value);

        return TypeIsolation::ITI;
    }

    public function plancher_bas(): ?PlancherBas
    {
        return $this->liaison->plancher_id ? $this->enveloppe->parois()->planchers_bas()->find(id: $this->liaison->plancher_id) : null;
    }

    /**
     * TODO: Définir l'état d'isolation par défaut des planchers bas à isolation répartie
     */
    public function type_isolation_plancher_bas(): ?TypeIsolation
    {
        if ($this->liaison->type !== TypeLiaison::PLANCHER_BAS_MUR)
            return null;
        if (false === $this->plancher_bas()->est_isole())
            return TypeIsolation::NON_ISOLE;
        if (false === $this->plancher_bas()->isolation()->type_isolation->inconnu())
            return TypeIsolation::from($this->plancher_bas()->isolation()->type_isolation->value);
        return TypeIsolation::ITE;
    }

    public function plancher_haut(): ?PlancherHaut
    {
        return $this->liaison->plancher_id ? $this->enveloppe->parois()->planchers_hauts()->find(id: $this->liaison->plancher_id) : null;
    }

    public function type_isolation_plancher_haut(): ?TypeIsolation
    {
        if ($this->liaison->type !== TypeLiaison::PLANCHER_HAUT_MUR)
            return null;
        if (false === $this->plancher_haut()->est_isole())
            return TypeIsolation::NON_ISOLE;
        if (false === $this->plancher_haut()->isolation()->type_isolation->inconnu())
            return TypeIsolation::from($this->plancher_haut()->isolation()->type_isolation->value);
        return TypeIsolation::ITE;
    }

    public function baie(): ?Baie
    {
        return $this->liaison->ouverture_id ? $this->enveloppe->parois()->baies()->find(id: $this->liaison->ouverture_id) : null;
    }

    public function porte(): ?Porte
    {
        return $this->liaison->ouverture_id ? $this->enveloppe->parois()->portes()->find(id: $this->liaison->ouverture_id) : null;
    }

    public function type_pose_ouverture(): ?TypePose
    {
        if ($this->liaison->type !== TypeLiaison::MENUISERIE_MUR)
            return null;
        if (null !== $this->baie()?->caracteristique()->menuiserie?->type_pose)
            return TypePose::from($this->baie()->caracteristique()->menuiserie->type_pose->value);
        if (null !== $this->porte()?->caracteristique()->menuiserie->type_pose)
            return TypePose::from($this->porte()->caracteristique()->menuiserie->type_pose->value);
        return null;
    }

    public function presence_retour_isolation(): ?bool
    {
        if ($this->liaison->type !== TypeLiaison::MENUISERIE_MUR)
            return null;
        if (null !== $this->baie()?->caracteristique()->menuiserie?->presence_retour_isolation)
            return $this->baie()?->caracteristique()->menuiserie?->presence_retour_isolation;
        if (null !== $this->porte()?->caracteristique()->menuiserie->presence_retour_isolation)
            return $this->porte()->caracteristique()->menuiserie->presence_retour_isolation;
        return false;
    }

    public function largeur_dormant(): ?int
    {
        if ($this->liaison->type !== TypeLiaison::MENUISERIE_MUR)
            return null;
        if (null !== $this->baie()?->caracteristique()->menuiserie?->largeur_dormant)
            return $this->baie()->caracteristique()->menuiserie->largeur_dormant;
        if (null !== $this->porte()?->caracteristique()->menuiserie->largeur_dormant)
            return $this->porte()->caracteristique()->menuiserie->largeur_dormant;
        return 50;
    }

    public function type_baie(): ?Enum
    {
        return $this->liaison->type === TypeLiaison::MENUISERIE_MUR
            ? $this->baie()?->caracteristique()->type
            : null;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function liaison(): Liaison
    {
        return $this->liaison;
    }

    public function longueur(): float
    {
        return $this->longueur;
    }

    public function kpt(): ?float
    {
        return $this->kpt;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }
}
