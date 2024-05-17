<?php

namespace App\Domain\Audit\Enum;

use App\Domain\Common\Enum\Enum;

enum TypeJustificatif: int implements Enum
{
    case PLAN = 1;
    case PLAN_SITUATION_OU_PLAN_MASSE = 2;
    case DIAGNOSTIC_SURFACE_HABITABLE = 3;
    case AVIS_TAXE_HABITATION = 4;
    case RELEVE_PROPRIETE = 5;
    case REGLEMENT_COPROPRIETE = 6;
    case DESCRIPTIFS_EQUIPEMENTS_COLLECTIFS = 7;
    case DESCRIPTIFS_EQUIPEMENTS_INDIVIDUELS = 8;
    case CONTRAT_MAINTENANCE = 9;
    case NOTICES_TECHNIQUES_EQUIPEMENTS = 10;
    case PERMIS_CONSTRUIRE = 11;
    case ETUDE_THERMIQUE_REGLEMENTAIRE = 12;
    case RAPPORT_PERMEABILITE_AIR = 13;
    case RAPPORT_COMPOSITION_PAROIS = 14;
    case FACTURES_TRAVAUX = 15;
    case PHOTOGRAPHIES_TRAVAUX = 16;
    case JUSTIFICATIFS_CREDIT_IMPOT = 17;
    case DECLARATION_PREALABLE_TRAVAUX = 18;
    case CAHIER_CHARGES = 19;
    case URL_API = 20;

    public function id(): int
    {
        return $this->value;
    }

    public function lib(): string
    {
        return match ($this) {
            self::PLAN => 'Plans de la maison, de l\'appartement ou de l\'immeuble',
            self::PLAN_SITUATION_OU_PLAN_MASSE => 'Plans de situation ou plan de masse du bâtiment',
            self::DIAGNOSTIC_SURFACE_HABITABLE => 'Diagnostic surface habitable',
            self::AVIS_TAXE_HABITATION => 'Avis de taxe d\'habitation',
            self::RELEVE_PROPRIETE => 'Relevé de propriété',
            self::REGLEMENT_COPROPRIETE => 'Règlement de copropriété',
            self::DESCRIPTIFS_EQUIPEMENTS_COLLECTIFS => 'Descriptifs des équipements collectifs fournis par le propriétaire des installations collectives ou le syndic de copropriété',
            self::DESCRIPTIFS_EQUIPEMENTS_INDIVIDUELS => 'Descriptifs des équipements individuels des logements non visités par le diagnostiqueur, fournis par le gestionnaire professionnel unique du bâtiment dans le cas d\'un propriétaire unique certifiant que tous les lots font l\'objet d\'une gestion homogène',
            self::CONTRAT_MAINTENANCE => 'Contrat de maintenance ou d\'entretien des équipements',
            self::NOTICES_TECHNIQUES_EQUIPEMENTS => 'Notices techniques des équipements, y compris celles mise à disposition publiquement par les fabricants',
            self::PERMIS_CONSTRUIRE => 'Permis de construire du bâtiment et, le cas échéant, de ses extensions',
            self::ETUDE_THERMIQUE_REGLEMENTAIRE => 'Étude thermique réglementaire',
            self::RAPPORT_PERMEABILITE_AIR => 'Rapport de mesure de la perméabilité à l\'air',
            self::RAPPORT_COMPOSITION_PAROIS => 'Rapport mentionnant la composition des parois, obtenue par sondage',
            self::FACTURES_TRAVAUX => 'Factures de travaux ou bordereaux de livraison décrivant les travaux réalisés, mentionnant l\'adresse du bien',
            self::PHOTOGRAPHIES_TRAVAUX => 'Photographies des travaux d\'isolation, permettant d\'identifier le bien et la paroi concernée',
            self::JUSTIFICATIFS_CREDIT_IMPOT => 'Justificatifs d\'obtention d\'un crédit d\'impôt ou d\'une prime de transition énergétique (CITE, MaPrimeRénov\')',
            self::DECLARATION_PREALABLE_TRAVAUX => 'Déclaration préalable des travaux de rénovation, dans le cas où cette procédure était nécessaire (par exemple pour une isolation thermique par l\'extérieur)',
            self::CAHIER_CHARGES => 'Cahier des charges ou programme de travaux',
            self::URL_API => 'URL/API',
        };
    }
}
