<?php

namespace App\Database\Opendata\Baie;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Baie\Enum\{InclinaisonVitrage as EnumInclinaisonVitrage, Mitoyennete, NatureGazLame, NatureMenuiserie, TypeBaie, TypeFermeture, TypePose, TypeVitrage};
use App\Domain\Baie\ValueObject\{Caracteristique, EpaisseurLameAir, InclinaisonVitrage, LargeurDormant, Surface, Sw, Ug, Ujn, Uw};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Identifier\Reference;
use App\Domain\Paroi\ValueObject\OrientationParoi;

final class XMLBaieReader extends XMLReaderIterator
{
    public function __construct(private XMLDoubleFenetreReader $double_fenetre_reader)
    {
    }

    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->get()->findOneOrError('.//reference')->getValue();
    }

    public function reference_paroi(): ?string
    {
        return $this->get()->findOne('.//reference_paroi')?->getValue();
    }

    public function reference_lnc(): ?string
    {
        return $this->get()->findOne('.//reference_lnc')?->getValue();
    }

    public function description(): string
    {
        return $this->get()->findOne('.//description')?->getValue() ?? "Porte";
    }

    public function surface_aue(): ?float
    {
        return ($value = $this->get()->findOne('.//surface_aue')?->getValue()) ? (float) $value : null;
    }

    public function enum_type_adjacence_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_adjacence_id')->getValue();
    }

    public function enum_type_pose_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_pose_id')->getValue();
    }

    public function enum_type_pose(): TypePose
    {
        return TypePose::from($this->enum_type_pose_id());
    }

    public function enum_orientation_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_orientation_id')->getValue();
    }

    public function enum_orientation(): ?Orientation
    {
        return Orientation::try_from_enum_orientation_id($this->enum_orientation_id());
    }

    public function enum_type_baie_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_baie_id')->getValue();
    }

    public function enum_type_baie(): TypeBaie
    {
        return TypeBaie::from_enum_type_baie_id($this->enum_type_baie_id());
    }

    public function enum_type_materiaux_menuiserie_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_materiaux_menuiserie_id')->getValue();
    }

    public function enum_type_materiaux_menuiserie(): NatureMenuiserie
    {
        return NatureMenuiserie::from_enum_type_materiaux_menuiserie_id($this->enum_type_materiaux_menuiserie_id());
    }

    public function enum_type_vitrage_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_vitrage_id')->getValue();
    }

    public function enum_type_vitrage(): TypeVitrage
    {
        return TypeVitrage::try_from_opendata($this->enum_type_vitrage_id(), $this->vitrage_vir());
    }

    public function enum_inclinaison_vitrage_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_inclinaison_vitrage_id')->getValue();
    }

    public function enum_inclinaison_vitrage(): EnumInclinaisonVitrage
    {
        return EnumInclinaisonVitrage::from_enum_inclinaison_vitrage_id($this->enum_inclinaison_vitrage_id());
    }

    public function enum_type_gaz_lame_id(): ?int
    {
        return (null !== $value = $this->get()->findOne('.//enum_type_gaz_lame_id')?->getValue()) ? (int) $value : null;
    }

    public function enum_type_gaz_lame(): ?NatureGazLame
    {
        return ($value = $this->enum_type_gaz_lame_id()) ? NatureGazLame::from_enum_type_gaz_lame_id($value) : null;
    }

    public function enum_type_fermeture_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_fermeture_id')->getValue();
    }

    public function enum_type_fermeture(): TypeFermeture
    {
        return TypeFermeture::from_enum_type_fermeture_id($this->enum_type_fermeture_id());
    }

    public function epaisseur_lame(): ?EpaisseurLameAir
    {
        return ($value = $this->get()->findOne('.//epaisseur_lame')?->getValue()) ? EpaisseurLameAir::from($value) : null;
    }

    public function vitrage_vir(): ?bool
    {
        return (null !== $value = $this->get()->findOne('.//vitrage_vir')?->getValue()) ? (bool) $value : null;
    }

    public function presence_protection_solaire_hors_fermeture(): bool
    {
        return (bool) $this->get()->findOneOrError('.//presence_protection_solaire_hors_fermeture')->getValue();
    }

    public function presence_retour_isolation(): bool
    {
        return (bool) $this->get()->findOneOrError('.//presence_retour_isolation')->getValue();
    }

    public function presence_joint(): bool
    {
        return (bool) $this->get()->findOneOrError('.//presence_joint')->getValue();
    }

    public function largeur_dormant(): LargeurDormant
    {
        return LargeurDormant::from((float) $this->get()->findOneOrError('.//largeur_dormant')->getValue() * 10);
    }

    public function surface_totale(): float
    {
        return (float) $this->get()->findOneOrError('.//surface_totale_baie')->getValue();
    }

    public function nombre(): int
    {
        return \max((int) $this->get()->findOneOrError('.//nb_baie')->getValue(), 1);
    }

    public function ug_saisi(): ?Ug
    {
        return ($value = $this->get()->findOne('.//ug_saisi')?->getValue()) ? Ug::from($value) : null;
    }

    public function uw_saisi(): ?Uw
    {
        return ($value = $this->get()->findOne('.//uw_saisi')?->getValue()) ? Uw::from($value) : null;
    }

    public function ujn_saisi(): ?Ujn
    {
        return ($value = $this->get()->findOne('.//ujn_saisi')?->getValue()) ? Ujn::from($value) : null;
    }

    public function sw_saisi(): ?Sw
    {
        return ($value = $this->get()->findOne('.//sw_saisi')?->getValue()) ? Sw::from($value) : null;
    }

    // Données déduites

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function orientation(): OrientationParoi
    {
        return OrientationParoi::from($this->enum_orientation()->to_azimut());
    }

    public function inclinaison_vitrage(): InclinaisonVitrage
    {
        return InclinaisonVitrage::from($this->enum_inclinaison_vitrage()->to_int());
    }

    public function surface(): Surface
    {
        return Surface::from($this->surface_totale() / $this->nombre());
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            presence_joint: $this->presence_joint(),
            presence_retour_isolation: $this->presence_retour_isolation(),
            surface: $this->surface(),
            largeur_dormant: $this->largeur_dormant(),
            type_pose: $this->enum_type_pose(),
            type_baie: $this->enum_type_baie(),
            nature_menuiserie: $this->enum_type_materiaux_menuiserie(),
            type_vitrage: $this->enum_type_vitrage(),
            inclinaison_vitrage: $this->inclinaison_vitrage(),
            epaisseur_lame: $this->epaisseur_lame(),
            nature_gaz_lame: $this->enum_type_gaz_lame(),
            type_fermeture: $this->enum_type_fermeture(),
            ug: $this->ug_saisi(),
            uw: $this->uw_saisi(),
            ujn: $this->ujn_saisi(),
            sw: $this->sw_saisi(),
        );
    }

    public function double_fenetre_reader(): ?XMLDoubleFenetreReader
    {
        return ($value = $this->get()->findOne('.//baie_vitree_double_fenetre')) ? $this->double_fenetre_reader->read($value) : null;
    }

    public function read(XMLElement $xml): self
    {
        $this->array = $xml->findMany('//baie_vitree_collection/baie_vitree');
        return $this;
    }
}
