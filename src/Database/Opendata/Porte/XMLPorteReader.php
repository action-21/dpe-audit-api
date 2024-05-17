<?php

namespace App\Database\Opendata\Porte;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Reference;
use App\Domain\Porte\Enum\{Mitoyennete, NatureMenuiserie, TypePorte, TypePose};
use App\Domain\Porte\ValueObject\{Caracteristique, LargeurDormant, Surface, Uporte};

final class XMLPorteReader extends XMLReaderIterator
{
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

    public function enum_type_porte_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_porte_id')->getValue();
    }

    public function enum_type_pose_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_pose_id')->getValue();
    }

    public function enum_etat_composant_id(): ?int
    {
        return (null !== $value = $this->get()->findOne('.//enum_etat_composant_id')?->getValue()) ? (int) $value : null;
    }

    public function type_porte(): TypePorte
    {
        return TypePorte::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    public function type_pose(): TypePose
    {
        return TypePose::from_enum_type_pose_id($this->enum_type_pose_id());
    }

    public function nombre(): int
    {
        return ($value = $this->get()->findOne('.//nb_porte')?->getValue()) ? (int) $value : 1;
    }

    public function surface(): Surface
    {
        return Surface::from((float) $this->get()->findOneOrError('.//surface_porte')->getValue());
    }

    public function largeur_dormant(): ?LargeurDormant
    {
        return ($value = $this->get()->findOne('.//largeur_dormant')?->getValue()) ? LargeurDormant::from((float) $value * 10) : null;
    }

    public function presence_retour_isolation(): ?bool
    {
        return (null !== $value = $this->get()->findOne('.//presence_retour_isolation')?->getValue()) ? (bool)(int) $value : null;
    }

    public function presence_joint(): bool
    {
        return (bool)(int) $this->get()->findOneOrError('.//presence_joint')->getValue();
    }

    public function uporte_saisi(): ?Uporte
    {
        return (null !== $value = $this->get()->findOne('.//uporte_saisi')?->getValue()) ? Uporte::from((float) $value) : null;
    }

    // Données déduites

    public function mitoyennete(): Mitoyennete
    {
        return Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function surface_unitaire(): Surface
    {
        return Surface::from($this->surface()->valeur() / $this->nombre());
    }

    public function enum_nature_menuiserie(): NatureMenuiserie
    {
        return NatureMenuiserie::from_enum_type_porte_id($this->enum_type_porte_id());
    }

    // Données intermédiaires

    public function uporte(): Uporte
    {
        return Uporte::from((float) $this->get()->findOneOrError('.//uporte')->getValue());
    }

    public function b(): float
    {
        return (float) $this->get()->findOneOrError('.//b')->getValue();
    }

    public function caracteristique(): Caracteristique
    {
        return new Caracteristique(
            nature_menuiserie: $this->enum_nature_menuiserie(),
            type_porte: $this->type_porte(),
            type_pose: $this->type_pose(),
            surface: $this->surface_unitaire(),
            largeur_dormant: $this->largeur_dormant(),
            presence_retour_isolation: $this->presence_retour_isolation(),
            presence_joint: $this->presence_joint(),
            uporte: $this->uporte_saisi(),
        );
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = $xml->findMany('.//porte_collection//porte');
        return $this;
    }
}
