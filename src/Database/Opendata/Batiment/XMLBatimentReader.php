<?php

namespace App\Database\Opendata\Batiment;

use App\Database\Opendata\XMLElement;
use App\Domain\Batiment\Enum\{ClasseAltitude, PeriodeConstruction};
use App\Domain\Batiment\ValueObject\{Adresse, Altitude, AnneeConstruction, Caracteristique, Hauteur, Logements, SurfaceHabitable};
use App\Domain\Common\Identifier\Uuid;

final class XMLBatimentReader
{
    private XMLElement $xml;

    public function __construct(private XMLNiveauReader $niveau_reader,)
    {
    }

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function enum_classe_altitude_id(): int
    {
        return (int) $this->xml->findOneOrError('//enum_classe_altitude_id')->getValue();
    }

    public function enum_classe_altitude(): ClasseAltitude
    {
        return ClasseAltitude::from_enum_classe_altitude_id($this->enum_classe_altitude_id());
    }

    public function enum_periode_construction_id(): int
    {
        return (int) $this->xml->findOneOrError('//enum_periode_construction_id')->getValue();
    }

    public function periode_construction(): PeriodeConstruction
    {
        return PeriodeConstruction::from_enum_periode_construction_id($this->enum_periode_construction_id());
    }

    public function nombre_appartement(): ?int
    {
        return ($value = $this->xml->findOne('//nombre_appartement')?->getValue()) ? (int) $value : null;
    }

    public function nombre_niveau(): int
    {
        return ($value = $this->xml->findOne('//nombre_niveau_immeuble')?->getValue()) ? (int) $value : 1;
    }

    public function hsp(): float
    {
        return (float) $this->xml->findOneOrError('//hsp')->getValue();
    }

    public function surface_habitable_immeuble(): ?float
    {
        return ($value = $this->xml->findOne('//surface_habitable_immeuble')) ? (float) $value->getValue() : null;
    }

    public function surface_habitable_logement(): float
    {
        return (float) $this->xml->findOneOrError('//surface_habitable_logement')->getValue();
    }

    public function adresse(): Adresse
    {
        $adresse = $this->xml->findOneOrError('//adresse_bien');

        return new Adresse(
            label: $adresse->findOneOfOrError(['.//ban_label', './/adresse_brut'])->getValue(),
            code_postal: $adresse->findOneOfOrError(['.//ban_postcode', './/code_postal_brut'])->getValue(),
            commune: $adresse->findOneOfOrError(['.//ban_city', './/nom_commune_brut'])->getValue(),
            ban_id: ($value = $adresse->findOne('.//ban_id')?->getValue()) ? (string) $value : null,
            rnb_id: null,
        );
    }

    // Données déduites

    public function altitude(): Altitude
    {
        return Altitude::from($this->enum_classe_altitude()->to_int());
    }

    public function annee_construction(): AnneeConstruction
    {
        return AnneeConstruction::from($this->periode_construction()->to_int());
    }

    public function logements(): Logements
    {
        return Logements::from($this->nombre_appartement() ?? 1);
    }

    public function hauteur_sous_plafond(): Hauteur
    {
        $value = $this->hsp();
        return ($value / $this->nombre_niveau()) < 2.2
            ? Hauteur::from($value * $this->nombre_niveau())
            : Hauteur::from($value);
    }

    public function surface_habitable(): SurfaceHabitable
    {
        return (null === $value = $this->surface_habitable_immeuble())
            ? SurfaceHabitable::from($this->surface_habitable_logement())
            : SurfaceHabitable::from($value);
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            altitude: $this->altitude(),
            annee_construction: $this->annee_construction(),
            nombre_logements: $this->logements(),
        );
    }

    public function xml(): XMLElement
    {
        return $this->xml;
    }

    public function niveau_reader(): XMLNiveauReader
    {
        return $this->niveau_reader->read($this);
    }

    public function read(XMLElement $xml): self
    {
        $this->xml = $xml;
        return $this;
    }
}
