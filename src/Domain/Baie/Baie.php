<?php

namespace App\Domain\Baie;

use App\Domain\Baie\Enum\{Mitoyennete, TypePose};
use App\Domain\Baie\ValueObject\{Caracteristique, DoubleFenetre, Orientation};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use App\Domain\MasqueProche\{MasqueProche, MasqueProcheCollection};
use App\Domain\Paroi\{Ouverture, ParoiOpaque, TypeParoi};

/**
 * Baie vitrée donnant sur l'extérieur ou sur un local non chauffé
 */
final class Baie implements Ouverture
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private ?ParoiOpaque $paroi_opaque,
        private ?Lnc $local_non_chauffe,
        private ?Mitoyennete $mitoyennete,
        private ?Orientation $orientation,
        private Caracteristique $caracteristique,
        private ?DoubleFenetre $double_fenetre,
        private MasqueProcheCollection $masque_proche_collection,
    ) {
    }

    public static function create(
        Enveloppe $enveloppe,
        Mitoyennete $mitoyennete,
        Orientation $orientation,
        string $description,
        Caracteristique $caracteristique,
        ?DoubleFenetre $double_fenetre,
        ?ParoiOpaque $paroi_opaque,
        ?Lnc $local_non_chauffe,
    ): self {
        $entity = new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            caracteristique: $caracteristique,
            double_fenetre: $double_fenetre,
            orientation: $orientation,
            mitoyennete: $mitoyennete,
            paroi_opaque: $paroi_opaque,
            local_non_chauffe: $local_non_chauffe,
            masque_proche_collection: new MasqueProcheCollection(),
        );
        return match (true) {
            null !== $paroi_opaque => $entity->set_paroi_opaque(entity: $paroi_opaque),
            null !== $local_non_chauffe => $entity->set_local_non_chauffe(entity: $local_non_chauffe),
            default => $entity->set_mitoyennete(mitoyennete: $mitoyennete)->set_orientation(orientation: $orientation),
        };
    }

    public function update(
        string $description,
        Caracteristique $caracteristique,
        ?DoubleFenetre $double_fenetre,
    ): self {
        $this->description = $description;
        $this->caracteristique = $caracteristique;
        $this->double_fenetre = $double_fenetre;
        return $this;
    }

    public function set_mitoyennete(Mitoyennete $mitoyennete): self
    {
        $this->mitoyennete = $mitoyennete;
        $this->local_non_chauffe = null;
        $this->paroi_opaque = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_paroi_opaque(ParoiOpaque $entity): self
    {
        $this->paroi_opaque = $entity;
        $this->local_non_chauffe = null;
        $this->mitoyennete = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_local_non_chauffe(Lnc $entity): self
    {
        $this->local_non_chauffe = $entity;
        $this->mitoyennete = Mitoyennete::LOCAL_NON_CHAUFFE;
        $this->paroi_opaque = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_orientation(Orientation $orientation): self
    {
        if ($this->paroi_opaque) {
            $this->orientation = null;
            return $this;
        }
        $this->orientation = $orientation;
        return $this;
    }

    public function controle_coherence(): void
    {
        if ($this->local_non_chauffe && false === $this->local_non_chauffe->type_lnc()->type_paroi_applicable($this->type_paroi())) {
            throw new \DomainException('Type de local non chauffé non applicable');
        }
        if (null === $this->local_non_chauffe && Mitoyennete::LOCAL_NON_CHAUFFE === $this->mitoyennete) {
            throw new \DomainException('Local non chauffé non défini');
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

    /** @inheritdoc */
    public function description(): string
    {
        return $this->description;
    }

    public function caracteristique(): Caracteristique
    {
        return $this->caracteristique;
    }

    /** @inheritdoc */
    public function paroi_opaque(): ?ParoiOpaque
    {
        return $this->paroi_opaque;
    }

    /** @inheritdoc */
    public function local_non_chauffe(): ?Lnc
    {
        return $this->paroi_opaque() ? $this->paroi_opaque()->local_non_chauffe() : $this->local_non_chauffe;
    }

    /** @inheritdoc */
    public function mitoyennete(): Mitoyennete
    {
        return $this->paroi_opaque ? Mitoyennete::from($this->paroi_opaque->mitoyennete()->id()) : $this->mitoyennete;
    }

    /** @inheritdoc */
    public function type_paroi(): TypeParoi
    {
        return TypeParoi::BAIE;
    }

    /** @inheritdoc */
    public function est_isole(): bool
    {
        return $this->caracteristique->type_vitrage?->est_isole() ?? false;
    }

    /** @inheritdoc */
    public function surface_deperditive(): float
    {
        return $this->caracteristique->surface->valeur();
    }

    /** @inheritdoc */
    public function surface(): float
    {
        return $this->caracteristique->surface->valeur();
    }

    /** @inheritdoc */
    public function presence_joint(): bool
    {
        return $this->caracteristique->presence_joint;
    }

    /** @inheritdoc */
    public function largeur_dormant(): ?float
    {
        return $this->caracteristique->largeur_dormant->valeur();
    }

    /** @inheritdoc */
    public function presence_retour_isolation(): ?bool
    {
        return $this->caracteristique->presence_retour_isolation;
    }

    /** @inheritdoc */
    public function type_pose(): TypePose
    {
        return $this->caracteristique->type_pose;
    }

    public function orientation(): ?Orientation
    {
        if ($this->paroi_opaque) {
            return ($value = $this->paroi_opaque->orientation()?->valeur()) ? Orientation::from($value) : null;
        }
        return $this->orientation;
    }

    public function fenetre_toit(): bool
    {
        return $this->paroi_opaque()?->type_paroi() === TypeParoi::PLANCHER_HAUT ?? false;
    }

    public function double_fenetre(): ?DoubleFenetre
    {
        return $this->double_fenetre;
    }

    public function masque_proche_collection(): MasqueProcheCollection
    {
        return $this->masque_proche_collection;
    }

    public function get_masque_proche(Id $id): ?MasqueProche
    {
        return $this->masque_proche_collection->find($id);
    }

    public function add_masque_proche(MasqueProche $entity): self
    {
        $this->masque_proche_collection->add($entity);
        return $this;
    }

    public function remove_masque_proche(MasqueProche $entity): self
    {
        $this->masque_proche_collection->removeElement($entity);
        return $this;
    }
}
