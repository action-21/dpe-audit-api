<?php

namespace App\Domain\Batiment;

use App\Domain\Audit\Audit;
use App\Domain\Batiment\Entity\{Niveau, NiveauCollection};
use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Batiment\ValueObject\{Adresse, Altitude, AnneeConstruction, Logements};
use App\Domain\Common\ValueObject\Id;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Logement\{Logement, LogementCollection};

final class Batiment
{
    public function __construct(
        private readonly \Stringable $id,
        private readonly Audit $audit,
        private readonly TypeBatiment $type_batiment,
        private Adresse $adresse,
        private Altitude $altitude,
        private AnneeConstruction $annee_construction,
        private Logements $nombre_logements,
        private NiveauCollection $niveau_collection,
        private LogementCollection $logement_collection,
        private ?Enveloppe $enveloppe = null,
    ) {
    }

    public static function create(
        Audit $audit,
        Adresse $adresse,
        Altitude $altitude,
        AnneeConstruction $annee_construction,
        Logements $nombre_logements,
    ): self {
        $entity = new self(
            id: Id::create(),
            audit: $audit,
            adresse: $adresse,
            type_batiment: $audit->perimetre_application()->type_batiment(),
            altitude: $altitude,
            annee_construction: $annee_construction,
            nombre_logements: $nombre_logements,
            niveau_collection: new NiveauCollection,
            logement_collection: new LogementCollection,
        );

        $entity->controle_coherence();
        return $entity;
    }

    public function update(
        Adresse $adresse,
        Altitude $altitude,
        AnneeConstruction $annee_construction,
        Logements $nombre_logements,
    ): self {
        $this->adresse = $adresse;
        $this->altitude = $altitude;
        $this->annee_construction = $annee_construction;
        $this->nombre_logements = $nombre_logements;
        $this->controle_coherence();
        return $this;
    }

    public function controle_coherence(): void
    {
        if ($this->logement_collection->surface_habitable() > $this->surface_habitable()) {
            throw new \InvalidArgumentException('La surface habitable des logements décrits est supérieure à la surface habitable du bâtiment');
        }
        if ($this->logement_collection->count() > $this->nombre_logements->valeur()) {
            throw new \InvalidArgumentException('Le nombre de logements décrits est supérieur au nombre de logements du bâtiment');
        }
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function audit(): Audit
    {
        return $this->audit;
    }

    public function enveloppe(): ?Enveloppe
    {
        return $this->enveloppe;
    }

    public function set_enveloppe(Enveloppe $enveloppe): self
    {
        $this->enveloppe = $enveloppe;
        return $this;
    }

    public function adresse(): Adresse
    {
        return $this->adresse;
    }

    public function type_batiment(): TypeBatiment
    {
        return $this->type_batiment;
    }

    public function altitude(): Altitude
    {
        return $this->altitude;
    }

    public function annee_construction(): AnneeConstruction
    {
        return $this->annee_construction;
    }

    public function nombre_logements(): Logements
    {
        return $this->nombre_logements;
    }

    public function nombre_niveaux(): int
    {
        return $this->niveau_collection->count();
    }

    /**
     * Surface habitable totale du bâtiment en m²
     */
    public function surface_habitable(): float
    {
        return $this->niveau_collection->surface_habitable();
    }

    /**
     * Hauteur sous plafond totale du bâtiment en m
     */
    public function hauteur_sous_plafond(): float
    {
        return $this->niveau_collection->hauteur_sous_plafond();
    }

    /**
     * Hauteur sous plafond moyenne du bâtiment en m
     */
    public function hauteur_sous_plafond_moyenne(): float
    {
        return $this->niveau_collection->hauteur_sous_plafond_moyenne();
    }

    /**
     * Surface habitable moyenne par logement en m²
     */
    public function surface_habitable_moyenne(): float
    {
        return $this->surface_habitable() / $this->nombre_logements->valeur();
    }

    /**
     * Volume habitable en m3
     */
    public function volume_habitable(): float
    {
        return $this->surface_habitable() * $this->hauteur_sous_plafond();
    }

    public function niveau_collection(): NiveauCollection
    {
        return $this->niveau_collection;
    }

    public function get_niveau(\Stringable $id): ?Niveau
    {
        return $this->niveau_collection->find($id);
    }

    public function add_niveau(Niveau $entity): self
    {
        $this->niveau_collection->add($entity);
        return $this;
    }

    public function remove_niveau(Niveau $entity): self
    {
        $this->niveau_collection->removeElement($entity);
        return $this;
    }

    public function logement_collection(): LogementCollection
    {
        return $this->logement_collection;
    }

    public function get_logement(\Stringable $id): ?Logement
    {
        return $this->logement_collection->find($id);
    }

    public function add_logement(Logement $entity): self
    {
        $this->logement_collection->add($entity);
        $this->controle_coherence();
        return $this;
    }

    public function remove_logement(Logement $entity): self
    {
        $this->logement_collection->removeElement($entity);
        return $this;
    }
}
