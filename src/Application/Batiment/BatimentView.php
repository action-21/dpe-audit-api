<?php

namespace App\Application\Batiment;

use App\Application\Batiment\View\{AdresseView, EclairageView, NiveauView, OccupationView, RefroidissementView, SituationView};
use App\Application\Enveloppe\EnveloppeView;
use App\Domain\Batiment\{Batiment, BatimentEngine};
use App\Domain\Common\Enum\Enum;

class BatimentView
{
    public function __construct(
        public readonly Enum $type_batiment,
        public readonly AdresseView $adresse,
        public readonly int $altitude,
        public readonly int $annee_construction,
        public readonly int $nombre_logements,
        public readonly float $surface_habitable,
        public readonly float $surface_habitable_moyenne,
        public readonly float $hauteur_sous_plafond,
        public readonly float $hauteur_sous_plafond_moyenne,
        public readonly float $volume_habitable,
        public readonly bool $effet_joule,
        public readonly Enum $classe_altitude,
        public readonly Enum $periode_construction,
        /** @var array<NiveauView> */
        public readonly array $niveau_collection,
        public readonly ?EnveloppeView $enveloppe,
        //public readonly array $logement_collection,
        public readonly ?OccupationView $occupation = null,
        public readonly ?SituationView $situation = null,
        public readonly ?EclairageView $eclairage = null,
        public readonly ?RefroidissementView $refroidissement = null,
    ) {
    }

    public static function from_entity(Batiment $entity): self
    {
        return new self(
            type_batiment: $entity->type_batiment(),
            adresse: AdresseView::from_vo($entity->adresse()),
            altitude: $entity->caracteristique()->altitude->valeur(),
            annee_construction: $entity->caracteristique()->annee_construction->valeur(),
            classe_altitude: $entity->caracteristique()->classe_altitude,
            periode_construction: $entity->caracteristique()->periode_construction,
            nombre_logements: $entity->caracteristique()->nombre_logements->valeur(),
            surface_habitable: $entity->surface_habitable(),
            surface_habitable_moyenne: $entity->surface_habitable_moyenne(),
            hauteur_sous_plafond: $entity->hauteur_sous_plafond(),
            hauteur_sous_plafond_moyenne: $entity->hauteur_sous_plafond_moyenne(),
            volume_habitable: $entity->volume_habitable(),
            effet_joule: $entity->effet_joule(),
            niveau_collection: NiveauView::from_entity_collection($entity->niveau_collection()),
            enveloppe: $entity->enveloppe() ? EnveloppeView::from_entity($entity->enveloppe()) : null,
        );
    }

    public static function from_engine(BatimentEngine $engine): self
    {
        $entity = $engine->input();
        return new self(
            type_batiment: $entity->type_batiment(),
            adresse: AdresseView::from_vo($entity->adresse()),
            altitude: $entity->caracteristique()->altitude->valeur(),
            annee_construction: $entity->caracteristique()->annee_construction->valeur(),
            classe_altitude: $entity->caracteristique()->classe_altitude,
            periode_construction: $entity->caracteristique()->periode_construction,
            nombre_logements: $entity->caracteristique()->nombre_logements->valeur(),
            surface_habitable: $entity->surface_habitable(),
            surface_habitable_moyenne: $entity->surface_habitable_moyenne(),
            hauteur_sous_plafond: $entity->hauteur_sous_plafond(),
            hauteur_sous_plafond_moyenne: $entity->hauteur_sous_plafond_moyenne(),
            volume_habitable: $entity->volume_habitable(),
            effet_joule: $entity->effet_joule(),
            niveau_collection: NiveauView::from_entity_collection($entity->niveau_collection()),
            enveloppe: $engine->enveloppe_engine() ? EnveloppeView::from_engine($engine->enveloppe_engine()) : null,
            occupation: OccupationView::from_engine($engine),
            situation: SituationView::from_engine($engine),
            eclairage: EclairageView::from_engine($engine),
            refroidissement: RefroidissementView::from_engine($engine),
        );
    }
}
