<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\XMLReader;
use App\Domain\Baie\Enum\{Mitoyennete, NatureGazLame, NatureMenuiserie, TypeBaie, TypeFermeture, TypePose, TypeSurvitrage, TypeVitrage};
use App\Domain\Baie\ValueObject\{Caracteristique, DoubleFenetre, Menuiserie, Survitrage, Vitrage};
use App\Domain\Common\ValueObject\Id;

final class XMLBaieReader extends XMLReader
{
    public function read_double_fenetre(): ?XMLDoubleFenetreReader
    {
        return ($xml = $this->xml()->findOne('.//baie_vitree_double_fenetre'))
            ? XMLDoubleFenetreReader::from($xml)
            : null;
    }

    public function read_masque_proche(): ?XMLMasqueProcheReader
    {
        $reader = XMLMasqueProcheReader::from($this->xml());
        return $reader->apply() ? $reader : null;
    }

    public function read_masque_lointain_homogene(): ?XMLMasqueLointainHomogeneReader
    {
        $reader = XMLMasqueLointainHomogeneReader::from($this->xml());
        return $reader->apply() ? $reader : null;
    }

    public function read_masque_lointain_non_homogene(): XMLMasqueLointainNonHomogeneReader
    {
        return XMLMasqueLointainNonHomogeneReader::from(
            $this->xml()->findMany('.//masque_lointain_non_homogene')
        );
    }

    public function match(string $reference): bool
    {
        return $reference === $this->reference() || $reference === $this->xml()->findOne('.//description')?->reference();
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->reference();
    }

    public function paroi_id(): ?Id
    {
        return $this->xml()->findOne('.//reference_paroi')?->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Baie non décrite';
    }

    public function mitoyennete(): Mitoyennete
    {
        return $this->enum_cfg_isolation_lnc_id() === 1
            ? Mitoyennete::LOCAL_NON_ACCESSIBLE
            : Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function orientation(): ?float
    {
        return $this->xml()->findOneOrError('.//enum_orientation_id')->orientation();
    }

    public function inclinaison(): float
    {
        return $this->xml()->findOneOrError('.//enum_inclinaison_vitrage_id')->inclinaison();
    }

    public function type_baie(): ?TypeBaie
    {
        return TypeBaie::from_enum_type_baie_id($this->enum_type_baie_id());
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            type: $this->type_baie(),
            surface: $this->surface(),
            inclinaison: $this->inclinaison(),
            type_fermeture: $this->type_fermeture(),
            presence_protection_solaire: $this->presence_protection_solaire(),
            annee_installation: null,
            presence_soubassement: $this->presence_soubassement(),
            menuiserie: $this->menuiserie(),
            vitrage: $this->vitrage(),
            ug: $this->ug_saisi(),
            uw: $this->uw_saisi(),
            ujn: $this->ujn_saisi(),
            sw: $this->sw_saisi(),
        );
    }

    public function menuiserie(): ?Menuiserie
    {
        return $this->nature_menuiserie() && $this->type_vitrage() ? new Menuiserie(
            nature: $this->nature_menuiserie(),
            type_pose: $this->type_pose(),
            presence_joint: $this->presence_joint(),
            presence_retour_isolation: $this->presence_retour_isolation(),
            largeur_dormant: $this->largeur_dormant(),
            presence_rupteur_pont_thermique: $this->presence_rupteur_pont_thermique(),
        ) : null;
    }

    public function vitrage(): ?Vitrage
    {
        return $this->type_vitrage() ? new Vitrage(
            type_vitrage: $this->type_vitrage(),
            survitrage: $this->survitrage(),
            nature_gaz_lame: $this->nature_gaz_lame(),
            epaisseur_lame: $this->epaisseur_lame(),
        ) : null;
    }

    public function survitrage(): ?Survitrage
    {
        return $this->type_survitrage() ? new Survitrage(
            type_survitrage: $this->type_survitrage(),
            epaisseur_lame: $this->epaisseur_lame(),
        ) : null;
    }

    public function double_fenetre(): ?DoubleFenetre
    {
        $reader = $this->read_double_fenetre();
        return $reader ? new DoubleFenetre(
            type: $reader->type_baie(),
            presence_soubassement: $reader->presence_soubassement(),
            menuiserie: $reader->menuiserie(),
            ug: $reader->ug_saisi(),
            uw: $reader->uw_saisi(),
            sw: $reader->sw_saisi(),
        ) : null;
    }

    public function type_pose(): TypePose
    {
        return TypePose::from_enum_type_pose_id($this->enum_type_pose_id());
    }

    public function nature_menuiserie(): ?NatureMenuiserie
    {
        return NatureMenuiserie::from_enum_type_materiaux_menuiserie_id($this->enum_type_materiaux_menuiserie_id());
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
        return $this->xml()->findOne('.//epaisseur_lame')?->intval();
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

    public function presence_protection_solaire(): ?bool
    {
        return $this->xml()->findOne('.//presence_protection_solaire_hors_fermeture')?->boolval();
    }

    public function presence_retour_isolation(): bool
    {
        return $this->xml()->findOneOrError('.//presence_retour_isolation')->boolval();
    }

    public function presence_joint(): bool
    {
        return $this->xml()->findOne('.//presence_joint')?->boolval() ?? false;
    }

    public function largeur_dormant(): int
    {
        return $this->xml()->findOneOrError('.//largeur_dormant')->intval() * 10;
    }

    public function ug_saisi(): ?float
    {
        return $this->xml()->findOne('.//ug_saisi')?->floatval();
    }

    public function uw_saisi(): ?float
    {
        return $this->xml()->findOne('.//uw_saisi')?->floatval();
    }

    public function ujn_saisi(): ?float
    {
        return $this->xml()->findOne('.//ujn_saisi')?->floatval();
    }

    public function sw_saisi(): ?float
    {
        return $this->xml()->findOne('.//sw_saisi')?->floatval();
    }

    public function enum_type_baie_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_baie_id')->intval();
    }

    public function enum_type_adjacence_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_adjacence_id')->intval();
    }

    public function enum_cfg_isolation_lnc_id(): ?int
    {
        return $this->xml()->findOne('.//enum_cfg_isolation_lnc_id')?->intval();
    }

    public function enum_type_materiaux_menuiserie_id(): int
    {
        return $this->xml()->findOne('.//enum_type_materiaux_menuiserie_id')->intval();
    }

    public function enum_type_vitrage_id(): int
    {
        return $this->xml()->findOne('.//enum_type_vitrage_id')->intval();
    }

    public function enum_type_gaz_lame_id(): ?int
    {
        return $this->xml()->findOne('.//enum_type_gaz_lame_id')?->intval();
    }

    public function enum_type_fermeture_id(): int
    {
        return $this->xml()->findOne('.//enum_type_fermeture_id')->intval();
    }

    public function enum_type_pose_id(): int
    {
        return $this->xml()->findOne('.//enum_type_pose_id')->intval();
    }

    public function vitrage_vir(): bool
    {
        return $this->xml()->findOne('.//vitrage_vir')?->boolval() ?? false;
    }

    public function surface_totale_baie(): float
    {
        return $this->xml()->findOneOrError('.//surface_totale_baie')->floatval();
    }

    public function nb_baie(): int
    {
        return $this->xml()->findOneOrError('.//nb_baie')->intval();
    }

    // Données intermédiaires

    public function b(): float
    {
        return $this->xml()->findOneOrError('.//b')->floatval();
    }

    public function uw(): float
    {
        return $this->xml()->findOneOrError('.//uw')->floatval();
    }

    public function u_menuiserie(): float
    {
        return $this->xml()->findOneOrError('.//u_menuiserie')->floatval();
    }

    public function sw(): float
    {
        return $this->xml()->findOneOrError('.//sw')->floatval();
    }

    public function fe1(): float
    {
        return $this->xml()->findOneOrError('.//fe1')->floatval();
    }

    public function fe2(): float
    {
        return $this->xml()->findOneOrError('.//fe2')->floatval();
    }
}
