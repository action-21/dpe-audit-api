<?php

namespace App\Domain\Porte;

use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Lnc\Lnc;
use App\Domain\Paroi\{Ouverture, ParoiOpaque, TypeParoi};
use App\Domain\Porte\Enum\{Mitoyennete, TypePorte, TypePose};
use App\Domain\Porte\ValueObject\Caracteristique;

/**
 * Porte donnant sur l'extérieur ou sur un local non chauffé
 */
final class Porte implements Ouverture
{
    public function __construct(
        private readonly Id $id,
        private readonly Enveloppe $enveloppe,
        private string $description,
        private Caracteristique $caracteristique,
        private ?Mitoyennete $mitoyennete,
        private ?ParoiOpaque $paroi_opaque,
        private ?Lnc $local_non_chauffe,
    ) {
    }

    public static function create(
        Enveloppe $enveloppe,
        string $description,
        Mitoyennete $mitoyennete,
        Caracteristique $caracteristique,
        ?ParoiOpaque $paroi_opaque = null,
        ?Lnc $local_non_chauffe = null,
    ): self {
        $entity = new self(
            id: Id::create(),
            enveloppe: $enveloppe,
            description: $description,
            mitoyennete: $mitoyennete,
            caracteristique: $caracteristique,
            paroi_opaque: $paroi_opaque,
            local_non_chauffe: $local_non_chauffe,
        );
        return match (true) {
            null !== $paroi_opaque => $entity->set_paroi_opaque(entity: $paroi_opaque),
            null !== $local_non_chauffe => $entity->set_local_non_chauffe(entity: $local_non_chauffe),
            default => $entity->set_mitoyennete(mitoyennete: $mitoyennete),
        };
    }

    public function update(string $description, Caracteristique $caracteristique,): self
    {
        $this->description = $description;
        $this->caracteristique = $caracteristique;
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

    public function set_mitoyennete(Mitoyennete $mitoyennete): self
    {
        $this->mitoyennete = $mitoyennete;
        $this->local_non_chauffe = null;
        $this->paroi_opaque = null;
        $this->controle_coherence();
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
        if (!\in_array($this->caracteristique->type_porte, TypePorte::cases_by_nature_menuiserie($this->caracteristique->nature_menuiserie))) {
            throw new \DomainException('Type de porte non applicable');
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
        return TypeParoi::PORTE;
    }

    /** @inheritdoc */
    public function est_isole(): bool
    {
        return $this->caracteristique->type_porte->est_isole();
    }

    /** @inheritdoc */
    public function surface_deperditive(): float
    {
        return $this->caracteristique->surface->valeur();
    }

    /** @inheritdoc */
    public function largeur_dormant(): ?float
    {
        return $this->caracteristique->largeur_dormant->valeur();
    }

    /** @inheritdoc */
    public function presence_joint(): bool
    {
        return $this->caracteristique->presence_joint;
    }

    /** @inheritdoc */
    public function presence_retour_isolation(): ?bool
    {
        return $this->caracteristique->presence_retour_isolation;
    }

    /** @inheritdoc */
    public function surface(): float
    {
        return $this->caracteristique->surface->valeur();
    }

    /** @inheritdoc */
    public function type_pose(): TypePose
    {
        return $this->caracteristique->type_pose;
    }
}
