<?php

namespace App\Serializer\Opendata;

use App\Domain\Audit\{Audit, AuditData};
use App\Domain\Audit\Entity\LogementCollection;
use App\Domain\Audit\Enum\{ClasseAltitude, Etat, PeriodeConstruction};
use App\Domain\Audit\ValueObject\{Adresse, Batiment};
use App\Domain\Chauffage\Chauffage;
use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Eclairage\Eclairage;
use App\Domain\Ecs\Ecs;
use App\Domain\Enveloppe\Enum\Exposition;
use App\Domain\Enveloppe\Enveloppe;
use App\Domain\Production\Production;
use App\Domain\Refroidissement\Refroidissement;
use App\Domain\Ventilation\Ventilation;

final class JSONAuditDeserializer
{
    public function denormalize(array $data): Audit
    {
        $adresse = Adresse::create(
            numero: $data['numero_voie_ban'] ?? null,
            nom: $data['nom_rue_ban'] ?? $data['adresse_brut'],
            code_postal: $data['code_postal_ban'] ?? $data['code_postal_brut'],
            code_commune: $data['code_insee_ban'] ?? $data['code_postal_brut'],
            commune: $data['nom_commune_ban'] ?? $data['nom_commune_brut'],
            ban_id: $data['identifiant_ban'] ?? null,
        );

        $batiment = Batiment::create(
            type: $data['type_batiment'],
            annee_construction: $data['annee_construction']
                ? Annee::from($data['annee_construction'])
                : PeriodeConstruction::from(str_replace(' ', '_', $data['periode_construction']))->annee(),
            altitude: ClasseAltitude::from_classe_altitude($data['classe_altitude'])->floatval(),
            logements: $data['nombre_appartement'] ?? 1,
            surface_habitable: $data['surface_habitable_logement'] ?? $data['surface_habitable_immeuble'],
            hauteur_sous_plafond: $data['hauteur_sous_plafond'],
            materiaux_anciens: false,
            rnb_id: null,
        );
        return new Audit(
            id: Id::from($data['numero_dpe']),
            date_etablissement: new \DateTimeImmutable($data['date_etablissement_dpe']),
            etat: Etat::PUBLIE,
            adresse: $adresse,
            batiment: $batiment,
            auditeur: null,
            logements: new LogementCollection,
            enveloppe: Enveloppe::create(
                exposition: Exposition::EXPOSITION_SIMPLE,
                q4pa_conv: null,
                presence_brasseurs_air: $data['presence_brasseur_air'] ?? false,
            ),
            ventilation: Ventilation::create(),
            chauffage: Chauffage::create(),
            ecs: Ecs::create(),
            refroidissement: Refroidissement::create(),
            eclairage: Eclairage::create(),
            production: Production::create(),
            data: AuditData::create(),
        );
    }
}
