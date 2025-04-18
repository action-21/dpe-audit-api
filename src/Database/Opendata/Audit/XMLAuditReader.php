<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\{XMLReader, XMLElement};
use App\Domain\Audit\Enum\{Perimetre, TypeBatiment};
use App\Domain\Audit\ValueObject\{Adresse, Batiment};
use App\Domain\Common\ValueObject\{Annee, Id};

final class XMLAuditReader extends XMLReader
{
    public static function from(XMLElement $xml): static
    {
        return parent::from(static::root($xml));
    }

    public function id(): Id
    {
        return $this->findOneOf(['//numero_dpe', '//reference_interne_projet'])?->id() ?? Id::create();
    }

    public function audit_batiment_id(): ?Id
    {
        return ($value = $this->findOne('//dpe_immeuble_associe')?->strval()) ? Id::from($value) : null;
    }

    public function reference(): ?string
    {
        return $this->findOneOf(['//numero_dpe', '//reference_interne_projet'])?->strval();
    }

    public function adresse(): Adresse
    {
        return Adresse::create(
            numero: null,
            nom: $this->libelle(),
            code_postal: $this->code_postal(),
            code_commune: $this->code_postal(),
            commune: $this->commune(),
            ban_id: $this->ban_id(),
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
            materiaux_anciens: $this->batiment_materiaux_anciens(),
            rnb_id: null,
        );
    }

    public function date_etablissement(): \DateTimeImmutable
    {
        $date = $this->findOneOfOrError(['//date_etablissement_audit', '//date_etablissement_dpe'])->strval();
        return new \DateTimeImmutable($date);
    }

    public function annee_etablissement(): Annee
    {
        return Annee::from((int) $this->date_etablissement()->format('Y'));
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
        if ($value = $this->findOne('//altitude')) {
            return $value->intval();
        }
        return match ($this->enum_classe_altitude_id()) {
            1 => 200,
            2 => 600,
            3 => 1000,
        };
    }

    public function batiment_materiaux_anciens(): bool
    {
        return $this->findOneOrError('//batiment_materiaux_anciens')->boolval();
    }

    public function annee_construction(): Annee
    {
        if ($value = $this->findOne('//annee_construction')) {
            return Annee::from($value->intval());
        }
        return match ($this->enum_periode_construction_id()) {
            1 => Annee::from(1947),
            2 => Annee::from(1974),
            3 => Annee::from(1977),
            4 => Annee::from(1982),
            5 => Annee::from(1988),
            6 => Annee::from(2000),
            7 => Annee::from(2005),
            8 => Annee::from(2012),
            9 => Annee::from(2021),
            10 => Annee::from((int) $this->date_etablissement()->format('Y')),
        };
    }

    public function logements(): int
    {
        return $this->nombre_appartement() ?? 1;
    }

    public function surface_habitable_batiment(): float
    {
        return $this->findOneOfOrError(['//surface_habitable_immeuble', '//surface_habitable_logement'])->floatval();
    }

    public function surface_habitable_logement(): ?float
    {
        return $this->findOne('//surface_habitable_logement')?->floatval();
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->hsp();
    }

    public function enum_methode_application_dpe_log_id(): int
    {
        return $this->findOneOrError('//enum_methode_application_dpe_log_id')->intval();
    }

    public function enum_classe_altitude_id(): int
    {
        return $this->findOneOrError('//enum_classe_altitude_id')->intval();
    }

    public function enum_periode_construction_id(): int
    {
        return $this->findOneOrError('//enum_periode_construction_id')->intval();
    }

    public function nombre_appartement(): ?int
    {
        return $this->findOne('//nombre_appartement')?->intval();
    }

    public function nombre_niveau(): int
    {
        return $this->findOne('//nombre_niveau_immeuble')?->intval() ?? 1;
    }

    public function hsp(): float
    {
        return $this->findOneOrError('//hsp')->floatval();
    }

    public function libelle(): string
    {
        return $this->findOneOfOrError([
            '//adresse_bien//ban_label',
            '//adresse_bien//adresse_brut',
        ])->strval();
    }

    public function code_postal(): string
    {
        return $this->findOneOfOrError([
            '//adresse_bien//ban_postcode',
            '//adresse_bien//code_postal_brut',
        ])->strval();
    }

    public function code_commune(): string
    {
        return $this->findOneOfOrError([
            '//adresse_bien//ban_citycode',
            '//adresse_bien//ban_postcode',
            '//adresse_bien//code_postal_brut',
        ])->strval();
    }

    public function commune(): string
    {
        return $this->findOneOfOrError([
            '//adresse_bien//ban_city',
            '//adresse_bien//nom_commune_brut',
        ])->strval();
    }

    public function ban_id(): ?string
    {
        return $this->findOne('//adresse_bien//ban_id')?->strval();
    }
}
