<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\Enveloppe\XMLParoiReader;
use App\Database\Opendata\XMLElement;
use App\Domain\Common\ValueObject\{Annee, Id, Inclinaison, Orientation};
use App\Domain\Enveloppe\Enum\Baie\{Materiau, NatureGazLame, TypeBaie, TypeFermeture, TypeSurvitrage, TypeVitrage};
use App\Domain\Enveloppe\Enum\TypePose;

final class XMLBaieReader extends XMLParoiReader
{
    public function double_fenetre(): ?XMLDoubleFenetreReader
    {
        return ($xml = $this->findOne('.//baie_vitree_double_fenetre'))
            ? XMLDoubleFenetreReader::from($xml)
            : null;
    }

    /**
     * @return XMLMasqueProcheReader[]
     */
    public function masques_proches(): array
    {
        $reader = XMLMasqueProcheReader::from($this->xml());
        return $reader->supports() ? [$reader] : [];
    }

    /**
     * @return XMLMasqueLointainReader[]
     */
    public function masques_lointains(): array
    {
        $readers = array_filter(
            array_map(
                fn(XMLElement $xml) => XMLMasqueLointainNonHomogeneReader::from($xml),
                $this->findMany('.//masque_lointain_non_homogene')
            ),
            fn(XMLMasqueLointainNonHomogeneReader $reader): bool => $reader->supports(),
        );

        $reader = XMLMasqueLointainHomogeneReader::from($this->xml());

        if ($reader->supports()) {
            $readers[] = $reader;
        }

        return $readers;
    }


    public function paroi_id(): ?Id
    {
        return $this->findOne('.//reference_paroi')?->id();
    }

    public function reference_paroi(): ?string
    {
        return $this->findOneOrError('.//reference_paroi')?->reference();
    }

    public function orientation(): ?Orientation
    {
        return ($enum = $this->findOneOrError('.//enum_orientation_id')?->intval())
            ? Orientation::from_enum_orientation_id($enum)
            : null;
    }

    public function inclinaison(): Inclinaison
    {
        return Inclinaison::from_enum_inclinaison_vitrage_id(
            $this->findOneOrError('.//enum_inclinaison_vitrage_id')->intval()
        );
    }

    public function annee_installation(): ?Annee
    {
        return null;
    }

    public function type_baie(): TypeBaie
    {
        return TypeBaie::from_enum_type_baie_id($this->enum_type_baie_id());
    }

    public function type_pose(): TypePose
    {
        return TypePose::from_enum_type_pose_id($this->enum_type_pose_id());
    }

    public function materiau(): ?Materiau
    {
        return Materiau::from_enum_type_materiaux_menuiserie_id($this->enum_type_materiaux_menuiserie_id());
    }

    public function type_vitrage(): ?TypeVitrage
    {
        return TypeVitrage::from_enum_type_vitrage_id(
            id: $this->enum_type_vitrage_id(),
            vitrage_vir: $this->vitrage_vir(),
        );
    }

    public function type_survitrage(): ?TypeSurvitrage
    {
        return TypeSurvitrage::from_enum_type_vitrage_id(
            id: $this->enum_type_vitrage_id(),
            vitrage_vir: $this->vitrage_vir(),
        );
    }

    public function epaisseur_survitrage(): ?float
    {
        return null;
    }

    public function nature_gaz_lame(): ?NatureGazLame
    {
        return ($value = $this->enum_type_gaz_lame_id()) ? NatureGazLame::from_enum_type_gaz_lame_id($value) : null;
    }

    public function type_fermeture(): TypeFermeture
    {
        return TypeFermeture::from_enum_type_fermeture_id($this->enum_type_fermeture_id());
    }

    public function epaisseur_lame(): ?int
    {
        return $this->findOne('.//epaisseur_lame')?->intval();
    }

    public function surface(): float
    {
        return $this->surface_totale_baie() / $this->nb_baie();
    }

    public function presence_soubassement(): ?bool
    {
        return match ($this->enum_type_baie_id()) {
            7 => false,
            8 => true,
            default => null,
        };
    }

    public function presence_rupteur_pont_thermique(): bool
    {
        return $this->enum_type_materiaux_menuiserie_id() === 6 ? true : false;
    }

    public function presence_protection_solaire(): bool
    {
        return $this->findOne('.//presence_protection_solaire_hors_fermeture')?->boolval() ?? false;
    }

    public function presence_retour_isolation(): bool
    {
        return $this->findOneOrError('.//presence_retour_isolation')->boolval();
    }

    public function presence_joint(): bool
    {
        return $this->findOne('.//presence_joint')?->boolval() ?? false;
    }

    public function largeur_dormant(): int
    {
        return $this->findOneOrError('.//largeur_dormant')->intval() * 10;
    }

    public function ug_saisi(): ?float
    {
        return $this->findOne('.//ug_saisi')?->floatval();
    }

    public function uw_saisi(): ?float
    {
        return $this->findOne('.//uw_saisi')?->floatval();
    }

    public function ujn_saisi(): ?float
    {
        return $this->findOne('.//ujn_saisi')?->floatval();
    }

    public function sw_saisi(): ?float
    {
        return $this->findOne('.//sw_saisi')?->floatval();
    }

    public function enum_type_baie_id(): int
    {
        return $this->findOneOrError('.//enum_type_baie_id')->intval();
    }

    public function enum_type_materiaux_menuiserie_id(): int
    {
        return $this->findOne('.//enum_type_materiaux_menuiserie_id')->intval();
    }

    public function enum_type_vitrage_id(): int
    {
        return $this->findOne('.//enum_type_vitrage_id')->intval();
    }

    public function enum_type_gaz_lame_id(): ?int
    {
        return $this->findOne('.//enum_type_gaz_lame_id')?->intval();
    }

    public function enum_type_fermeture_id(): int
    {
        return $this->findOne('.//enum_type_fermeture_id')->intval();
    }

    public function enum_type_pose_id(): int
    {
        return $this->findOne('.//enum_type_pose_id')->intval();
    }

    public function vitrage_vir(): bool
    {
        return $this->findOne('.//vitrage_vir')?->boolval() ?? false;
    }

    public function surface_totale_baie(): float
    {
        return $this->findOneOrError('.//surface_totale_baie')->floatval();
    }

    public function nb_baie(): int
    {
        return $this->findOneOrError('.//nb_baie')->intval();
    }

    // Données intermédiaires

    public function b(): float
    {
        return $this->findOneOrError('.//b')->floatval();
    }

    public function uw(): float
    {
        return $this->findOneOrError('.//uw')->floatval();
    }

    public function u_menuiserie(): float
    {
        return $this->findOneOrError('.//u_menuiserie')->floatval();
    }

    public function sw(): float
    {
        return $this->findOneOrError('.//sw')->floatval();
    }

    public function fe1(): float
    {
        return $this->findOneOrError('.//fe1')->floatval();
    }

    public function fe2(): float
    {
        return $this->findOneOrError('.//fe2')->floatval();
    }
}
