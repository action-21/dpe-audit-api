<?php

namespace App\Domain\Chauffage\Factory;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use App\Domain\Common\Type\Id;

final class GenerateurFactory
{
    private Id $id;
    private Chauffage $chauffage;
    private string $description;
    private bool $generateur_collectif;
    private bool $position_volume_chauffe;
    private ?int $annee_installation;

    public function initialise(
        Id $id,
        Chauffage $chauffage,
        string $description,
        bool $generateur_collectif,
        bool $position_volume_chauffe,
        ?int $annee_installation,
    ): self {
        $this->id = $id;
        $this->chauffage = $chauffage;
        $this->description = $description;
        $this->generateur_collectif = $generateur_collectif;
        $this->position_volume_chauffe = $position_volume_chauffe;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    private function build(
        TypeGenerateur $type,
        EnergieGenerateur $energie,
        ?bool $generateur_collectif = null,
        ?bool $position_volume_chauffe = null,
        ?TypeGenerateur $type_partie_chaudiere = null,
        ?EnergieGenerateur $energie_partie_chaudiere = null,
        ?Signaletique $signaletique = null,
        ?Id $generateur_mixte_id = null,
        ?Id $reseau_chaleur_id = null,
    ): Generateur {
        $entity = new Generateur(
            id: $this->id,
            chauffage: $this->chauffage,
            description: $this->description,
            type: $type,
            energie: $energie,
            type_partie_chaudiere: $type_partie_chaudiere,
            energie_partie_chaudiere: $energie_partie_chaudiere,
            signaletique: $signaletique ?? new Signaletique(),
            annee_installation: $this->annee_installation,
            generateur_collectif: $generateur_collectif ?? $this->generateur_collectif,
            position_volume_chauffe: $position_volume_chauffe ?? $this->position_volume_chauffe,
            generateur_mixte_id: $generateur_mixte_id,
            reseau_chaleur_id: $reseau_chaleur_id,
        );
        $entity->determine_categorie();
        $entity->controle();
        return $entity;
    }

    public function build_chaudiere(
        TypeGenerateur\TypeChaudiere $type,
        EnergieGenerateur\EnergieChaudiere $energie,
        Signaletique\Chaudiere $signaletique,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: $energie->to(),
            signaletique: $signaletique,
        );
    }

    public function build_chaudiere_electrique(
        Signaletique\ChaudiereElectrique $signaletique,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::CHAUDIERE_STANDARD,
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
        );
    }

    public function build_chaudiere_multi_batiment(
        EnergieGenerateur\EnergieChaudiere $energie,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::CHAUDIERE_MULTI_BATIMENT,
            energie: $energie->to(),
            generateur_collectif: true,
            position_volume_chauffe: false,
        );
    }

    public function build_pac_multi_batiment(Signaletique\Pac $signaletique,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::PAC_MULTI_BATIMENT,
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
            generateur_collectif: true,
            position_volume_chauffe: false,
        );
    }

    public function build_pac(
        TypeGenerateur\TypePac $type,
        Signaletique\Pac $signaletique,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
        );
    }

    public function build_pac_hybride(
        TypeGenerateur\TypePacHybride $type,
        TypeGenerateur\TypeChaudiere $type_partie_chaudiere,
        EnergieGenerateur\EnergieChaudiere $energie_partie_chaudiere,
        Signaletique\PacHybride $signaletique,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
            type_partie_chaudiere: $type_partie_chaudiere->to(),
            energie_partie_chaudiere: $energie_partie_chaudiere->to(),
        );
    }

    public function build_generateur_air_chaud_combustion(
        TypeGenerateur\TypeGenerateurAirChaud $type,
        EnergieGenerateur\EnergieGenerateurAirChaud $energie,
        Signaletique\Combustion $signaletique,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: $energie->to(),
            signaletique: $signaletique,
        );
    }

    public function build_generateur_air_chaud_electrique(): Generateur
    {
        return $this->build(
            type: TypeGenerateur::GENERATEUR_AIR_CHAUD,
            energie: EnergieGenerateur::ELECTRICITE,
            generateur_collectif: false,
        );
    }

    public function build_systeme_collectif_defaut(): Generateur
    {
        return $this->build(
            type: TypeGenerateur::SYSTEME_COLLECTIF_PAR_DEFAUT,
            energie: EnergieGenerateur::FIOUL,
            generateur_collectif: true,
        );
    }

    public function build_chauffage_electrique(
        TypeGenerateur\TypeChauffageElectrique $type,
        Signaletique\ChauffageElectrique $signaletique,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
            generateur_collectif: false,
        );
    }

    public function build_poele_insert(
        TypeGenerateur\TypePoeleInsert $type,
        EnergieGenerateur\EnergiePoeleInsert $energie,
        Signaletique\Combustion $signaletique,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: $energie->to(),
            signaletique: $signaletique,
            generateur_collectif: false,
        );
    }

    public function build_poele_insert_bois(
        TypeGenerateur\TypePoeleInsert $type,
        EnergieGenerateur\EnergiePoeleInsertBois $energie,
        Signaletique\PoeleInsertBois $signaletique,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: $energie->to(),
            signaletique: $signaletique,
            generateur_collectif: false,
        );
    }

    public function build_poele_bois_bouilleur(
        EnergieGenerateur\EnergiePoeleBouilleur $energie,
        Signaletique\Combustion $signaletique,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::POELE_BOUILLEUR,
            energie: $energie->to(),
            signaletique: $signaletique,
        );
    }

    public function build_radiateur_independant(
        EnergieGenerateur\EnergieRadiateurIndependant $energie,
        Signaletique\Combustion $signaletique,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::RADIATEUR_INDEPENDANT,
            energie: $energie->to(),
            signaletique: $signaletique,
            generateur_collectif: false,
        );
    }

    public function build_reseau_chaleur(): Generateur
    {
        return $this->build(
            type: TypeGenerateur::RESEAU_CHALEUR,
            energie: EnergieGenerateur::RESEAU_CHALEUR,
            generateur_collectif: true,
            position_volume_chauffe: false,
        );
    }
}
