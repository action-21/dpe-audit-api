<?php

namespace App\Database\Opendata\Enveloppe\Baie;

use App\Database\Opendata\Enveloppe\XMLParoiReader;
use App\Domain\Common\ValueObject\{Annee, Id, Inclinaison, Orientation};
use App\Domain\Enveloppe\Enum\Baie\{Materiau, NatureGazLame, TypeBaie, TypeFermeture, TypeSurvitrage, TypeVitrage};
use App\Domain\Enveloppe\Enum\TypePose;
use App\Domain\Enveloppe\ValueObject\Baie\{Composition, Menuiserie, Performance, Survitrage, Vitrage};

final class XMLBaieReader extends XMLParoiReader
{
    public function supports(): bool
    {
        return $this->nb_baie() > 0 && $this->surface() > 0;
    }

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
        $readers = [];

        foreach ($this->findMany('.//masque_lointain_non_homogene') as $xml) {
            $reader = XMLMasqueLointainNonHomogeneReader::from($xml);

            if ($reader->supports()) {
                $readers[] = $reader;
            }
        }

        $reader = XMLMasqueLointainHomogeneReader::from($this->xml());

        if ($reader->supports()) {
            $readers[] = $reader;
        }

        return $readers;
    }

    public function paroi_id(): ?Id
    {
        if (null === $this->reference_paroi()) {
            return null;
        }
        foreach ($this->enveloppe()->parois() as $reader) {
            if ($reader->reference() === $this->reference()) {
                continue;
            }
            if ($reader->reference() === $this->reference_paroi()) {
                return $reader->id();
            }
        }
        return null;
    }

    public function orientation(): ?Orientation
    {
        if (null === $value = $this->findOne('.//enum_orientation_id')?->intval()) {
            return null;
        }
        return Orientation::from_enum_orientation_id($value);
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

    public function type_pose(): ?TypePose
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

    public function composition(): Composition
    {
        return new Composition(
            type_baie: $this->type_baie(),
            type_pose: $this->type_pose(),
            materiau: $this->materiau(),
            presence_soubassement: $this->presence_soubassement(),
            vitrage: $this->vitrage(),
            menuiserie: $this->menuiserie(),
        );
    }

    public function menuiserie(): ?Menuiserie
    {
        if ($this->type_baie()->is_paroi_vitree()) {
            return null;
        }
        return Menuiserie::create(
            largeur_dormant: $this->largeur_dormant(),
            presence_joint: $this->presence_joint(),
            presence_retour_isolation: $this->presence_retour_isolation(),
            presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
        );
    }

    public function performance(): Performance
    {
        return Performance::create(
            ug: $this->ug_saisi(),
            uw: $this->uw_saisi(),
            ujn: $this->ujn_saisi(),
            sw: $this->sw_saisi(),
        );
    }

    public function vitrage(): ?Vitrage
    {
        if ($this->type_baie()->is_paroi_vitree()) {
            return null;
        }
        return Vitrage::create(
            type_vitrage: $this->type_vitrage(),
            nature_gaz_lame: $this->nature_gaz_lame(),
            epaisseur_lame: $this->epaisseur_lame(),
            survitrage: $this->survitrage(),
        );
    }

    public function survitrage(): ?Survitrage
    {
        if ($this->type_baie()->is_paroi_vitree()) {
            return null;
        }
        if (null === $this->type_survitrage()) {
            return null;
        }
        return Survitrage::create(
            type_survitrage: $this->type_survitrage(),
            epaisseur_lame: $this->epaisseur_survitrage(),
        );
    }
}
