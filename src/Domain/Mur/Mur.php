<?php

namespace App\Domain\Mur;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use App\Domain\Mur\Enum\{Mitoyennete, TypeIsolation};
use App\Domain\Mur\ValueObject\{Caracteristique, Isolation, Orientation};
use App\Domain\Paroi\{ParoiOpaque, TypeParoi};

/**
 * Mur donnant sur l'extérieur ou sur un local non chauffé
 */
final class Mur implements ParoiOpaque
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private ?Lnc $local_non_chauffe,
        private string $description,
        private Mitoyennete $mitoyennete,
        private Caracteristique $caracteristique,
        private Isolation $isolation,
        private Orientation $orientation,
    ) {
    }

    public static function create(
        Enveloppe $enveloppe,
        string $description,
        Mitoyennete $mitoyennete,
        Orientation $orientation,
        Caracteristique $caracteristique,
        Isolation $isolation,
        ?Lnc $local_non_chauffe = null,
    ): self {
        $entity = new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            mitoyennete: $mitoyennete,
            caracteristique: $caracteristique,
            isolation: $isolation,
            orientation: $orientation,
            local_non_chauffe: $local_non_chauffe,
        );
        return $local_non_chauffe
            ? $entity->set_local_non_chauffe(entity: $local_non_chauffe)
            : $entity->set_mitoyennete(mitoyennete: $mitoyennete);
    }

    public function update(
        string $description,
        Orientation $orientation,
        Caracteristique $caracteristique,
        Isolation $isolation,
    ): self {
        $this->description = $description;
        $this->orientation = $orientation;
        $this->caracteristique = $caracteristique;
        $this->isolation = $isolation;
        $this->controle_coherence();
        return $this;
    }

    public function set_local_non_chauffe(Lnc $entity): static
    {
        $this->local_non_chauffe = $entity;
        $this->mitoyennete = Mitoyennete::LOCAL_NON_CHAUFFE;
        $this->controle_coherence();
        return $this;
    }

    public function set_mitoyennete(Mitoyennete $mitoyennete): static
    {
        $this->mitoyennete = $mitoyennete;
        $this->local_non_chauffe = null;
        $this->controle_coherence();
        return $this;
    }

    public function controle_coherence(): void
    {
        $type_isolation_cases = TypeIsolation::cases_by_type_mur($this->caracteristique->type_mur);
        if (\count($type_isolation_cases) === 1) {
            $this->isolation = Isolation::create(
                type_isolation: \reset($type_isolation_cases),
                annnee_isolation: $this->isolation->annnee_isolation,
                epaisseur_isolant: $this->isolation->epaisseur_isolant,
                resistance_thermique: $this->isolation->resistance_thermique,
            );
        }
        if ($this->local_non_chauffe & false === $this->local_non_chauffe->type_lnc()->type_paroi_applicable($this->type_paroi())) {
            throw new \DomainException('Type de local non chauffé non applicable');
        }
        if ($this->mitoyennete = Mitoyennete::LOCAL_NON_CHAUFFE && null === $this->local_non_chauffe) {
            throw new \DomainException('Local non chauffé non défini');
        }
        if (!\in_array($this->isolation->type_isolation, $type_isolation_cases)) {
            throw new \DomainException('Type d\'isolation non applicable');
        }
        if ($this->isolation->annnee_isolation?->valeur() && $this->isolation->annnee_isolation->valeur() < $this->enveloppe->batiment()->annee_construction()->valeur()) {
            throw new \DomainException('Année d\'isolation antérieure à l\'année de construction');
        }
    }

    /** @inheritdoc */
    public function id(): Id
    {
        return $this->id;
    }

    /** @inheritdoc */
    public function enveloppe(): Enveloppe
    {
        return $this->enveloppe;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function caracteristique(): Caracteristique
    {
        return $this->caracteristique;
    }

    public function isolation(): Isolation
    {
        return $this->isolation;
    }

    /** @inheritdoc */
    public function local_non_chauffe(): ?Lnc
    {
        return $this->local_non_chauffe;
    }

    /** @inheritdoc */
    public function mitoyennete(): Mitoyennete
    {
        return $this->mitoyennete;
    }

    /** @inheritdoc */
    public function type_paroi(): TypeParoi
    {
        return TypeParoi::MUR;
    }

    /** @inheritdoc */
    public function surface(): float
    {
        return $this->caracteristique->surface->valeur();
    }

    /** @inheritdoc */
    public function surface_deperditive(): float
    {
        return $this->surface() - $this->enveloppe->paroi_collection()->search_ouverture()->search_by_paroi_opaque($this)->surface_deperditive();
    }

    /** @inheritdoc */
    public function paroi_lourde(): bool
    {
        return $this->caracteristique->inertie->lourde();
    }

    /** @inheritdoc */
    public function orientation(): Orientation
    {
        return $this->orientation;
    }

    /** @inheritdoc */
    public function est_isole(): bool
    {
        return (bool) $this->type_isolation_defaut()->est_isole();
    }

    /** @inheritdoc */
    public function facade(): bool
    {
        return $this->mitoyennete === Mitoyennete::EXTERIEUR;
    }

    public function epaisseur_structure(): float
    {
        return $this->caracteristique->epaisseur?->valeur ?? $this->caracteristique->type_mur->epaisseur_defaut();
    }

    /**
     * Type d'isolation par défaut
     */
    public function type_isolation_defaut(): TypeIsolation
    {
        return $this->isolation->type_isolation->defaut(
            annee_construction: $this->enveloppe->batiment()->annee_construction()->valeur(),
        );
    }

    /**
     * Année d'isolation par défaut
     */
    public function annnee_isolation_defaut(): ?int
    {
        if (false === $this->isolation->type_isolation->est_isole()) {
            return null;
        }
        return $this->isolation->annnee_isolation?->valeur()
            ?? $this->enveloppe->batiment()->annee_construction()->annee_isolation_defaut();
    }
}
