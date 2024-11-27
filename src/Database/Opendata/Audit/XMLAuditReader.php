<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLReader;
use App\Domain\Audit\Enum\Perimetre;
use App\Domain\Audit\Enum\TypeBatiment;
use App\Domain\Audit\ValueObject\Adresse;
use App\Domain\Audit\ValueObject\Batiment;
use App\Domain\Audit\ValueObject\Logement;
use App\Domain\Common\Type\Id;

final class XMLAuditReader extends XMLReader
{
    public function id(): Id
    {
        return $this->xml()->findOneOf(['//numero_dpe', '//reference_interne_projet'])?->id() ?? Id::create();
    }

    public function audit_batiment_id(): ?Id
    {
        return ($value = $this->xml()->findOne('//dpe_immeuble_associe')?->strval()) ? Id::from($value) : null;
    }

    public function reference(): ?string
    {
        return $this->xml()->findOneOf(['//numero_dpe', '//reference_interne_projet'])?->strval();
    }

    public function adresse(): Adresse
    {
        return Adresse::create(
            libelle: $this->libelle(),
            code_postal: $this->code_postal(),
            commune: $this->commune(),
            ban_id: $this->ban_id(),
            rnb_id: null,
        );
    }

    public function batiment(): Batiment
    {
        return Batiment::create(
            annee_construction: $this->annee_construction(),
            altitude: $this->altitude(),
            logements: $this->nombre_appartement() ?? 1,
            surface_habitable: $this->surface_habitable_batiment(),
            hauteur_sous_plafond: $this->hauteur_sous_plafond(),
        );
    }

    public function logement(): ?Logement
    {
        return $this->surface_habitable_logement() ? Logement::create(
            description: 'Logement',
            surface_habitable: $this->surface_habitable_logement(),
            hauteur_sous_plafond: $this->hauteur_sous_plafond(),
        ) : null;
    }

    public function date_etablissement(): \DateTimeImmutable
    {
        $date = $this->xml()->findOneOfOrError(['//date_etablissement_audit', '//date_etablissement_dpe'])->strval();
        return new \DateTimeImmutable($date);
    }

    public function perimetre(): Perimetre
    {
        return Perimetre::from_enum_methode_application_dpe_log_id(id: $this->enum_methode_application_dpe_log_id());
    }

    public function type_batiment(): TypeBatiment
    {
        return TypeBatiment::from_enum_methode_application_dpe_log_id(id: $this->enum_methode_application_dpe_log_id());
    }

    public function altitude(): int
    {
        if ($value = $this->xml()->findOne('//altitude')) {
            return $value->intval();
        }
        return match ($this->enum_classe_altitude_id()) {
            1 => 200,
            2 => 600,
            3 => 1000,
        };
    }

    public function annee_construction(): int
    {
        if ($value = $this->xml()->findOne('//annee_construction')) {
            return $value->intval();
        }
        return match ($this->enum_periode_construction_id()) {
            1 => 1947,
            2 => 1974,
            3 => 1977,
            4 => 1982,
            5 => 1988,
            6 => 2000,
            7 => 2005,
            8 => 2012,
            9 => 2021,
            10 => (int) $this->date_etablissement()->format('Y'),
        };
    }

    public function logements(): int
    {
        return $this->nombre_appartement() ?? 1;
    }

    public function surface_habitable_batiment(): float
    {
        return $this->xml()->findOneOfOrError(['//surface_habitable_immeuble', '//surface_habitable_logement'])->floatval();
    }

    public function surface_habitable_logement(): ?float
    {
        return $this->xml()->findOne('//surface_habitable_logement')?->floatval();
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->hsp();
    }

    public function enum_methode_application_dpe_log_id(): int
    {
        return $this->xml()->findOneOrError('//enum_methode_application_dpe_log_id')->intval();
    }

    public function enum_classe_altitude_id(): int
    {
        return $this->xml()->findOneOrError('//enum_classe_altitude_id')->intval();
    }

    public function enum_periode_construction_id(): int
    {
        return $this->xml()->findOneOrError('//enum_periode_construction_id')->intval();
    }

    public function nombre_appartement(): ?int
    {
        return $this->xml()->findOne('//nombre_appartement')?->intval();
    }

    public function nombre_niveau(): int
    {
        return $this->xml()->findOne('//nombre_niveau_immeuble')?->intval() ?? 1;
    }

    public function hsp(): float
    {
        return $this->xml()->findOneOrError('//hsp')->floatval();
    }

    public function libelle(): string
    {
        return $this->xml()->findOneOrError('//adresse_bien')->findOneOfOrError(['.//ban_label', './/adresse_brut'])->strval();
    }

    public function code_postal(): string
    {
        return $this->xml()->findOneOrError('//adresse_bien')->findOneOfOrError(['.//ban_postcode', './/code_postal_brut'])->strval();
    }

    public function commune(): string
    {
        return $this->xml()->findOneOrError('//adresse_bien')->findOneOfOrError(['.//ban_city', './/nom_commune_brut'])->strval();
    }

    public function ban_id(): ?string
    {
        return $this->xml()->findOneOrError('//adresse_bien')->findOne('.//ban_id')?->strval();
    }
}
