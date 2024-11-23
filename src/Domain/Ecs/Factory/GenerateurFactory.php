<?php

namespace App\Domain\Ecs\Factory;

use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Ecs\ValueObject\Signaletique;

final class GenerateurFactory
{
    private Id $id;
    private Ecs $ecs;
    private string $description;
    private bool $position_volume_chauffe;
    private bool $generateur_collectif;
    private ?int $annee_installation;

    public function initialise(
        Id $id,
        Ecs $ecs,
        string $description,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?int $annee_installation,
    ): self {
        $this->id = $id;
        $this->ecs = $ecs;
        $this->description = $description;
        $this->position_volume_chauffe = $position_volume_chauffe;
        $this->generateur_collectif = $generateur_collectif;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    private function build(
        TypeGenerateur $type,
        EnergieGenerateur $energie,
        int $volume_stockage = 0,
        ?bool $position_volume_chauffe = null,
        ?bool $generateur_collectif = null,
        ?Signaletique $signaletique = null,
        ?Id $generateur_mixte_id = null,
        ?Id $reseau_chaleur_id = null,
    ): Generateur {
        $entity = new Generateur(
            id: $this->id,
            ecs: $this->ecs,
            description: $this->description,
            type: $type,
            energie: $energie,
            signaletique: $signaletique ?? new Signaletique(),
            volume_stockage: $volume_stockage,
            position_volume_chauffe: $position_volume_chauffe ?? $this->position_volume_chauffe,
            generateur_collectif: $generateur_collectif ?? $this->generateur_collectif,
            annee_installation: $this->annee_installation,
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
        Signaletique\Combustion $signaletique,
        int $volume_stockage,
        ?Id $generateur_mixte_id,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: $energie->to(),
            signaletique: $signaletique,
            volume_stockage: $volume_stockage,
            generateur_mixte_id: $generateur_mixte_id,
        );
    }

    public function build_chaudiere_electrique(
        Signaletique\ChaudiereElectrique $signaletique,
        int $volume_stockage,
        ?Id $generateur_mixte_id,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::CHAUDIERE_STANDARD,
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
            volume_stockage: $volume_stockage,
            generateur_mixte_id: $generateur_mixte_id,
        );
    }

    public function build_poele_bouilleur(
        EnergieGenerateur\EnergiePoeleBouilleur $energie,
        Signaletique\Combustion $signaletique,
        int $volume_stockage,
        ?Id $generateur_mixte_id,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::POELE_BOUILLEUR,
            energie: $energie->to(),
            signaletique: $signaletique,
            generateur_mixte_id: $generateur_mixte_id,
            volume_stockage: $volume_stockage,
        );
    }

    public function build_chaudiere_multi_batiment(
        EnergieGenerateur\EnergieChaudiereMultiBatiment $energie,
        ?Id $generateur_mixte_id,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::CHAUDIERE_MULTI_BATIMENT,
            energie: $energie->to(),
            generateur_mixte_id: $generateur_mixte_id,
            position_volume_chauffe: false,
        );
    }

    public function build_pac_multi_batiment(?Id $generateur_mixte_id,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::PAC_MULTI_BATIMENT,
            energie: EnergieGenerateur::ELECTRICITE,
            generateur_mixte_id: $generateur_mixte_id,
            position_volume_chauffe: false,
        );
    }

    public function build_pac_double_service(
        Signaletique\Thermodynamique $signaletique,
        ?Id $generateur_mixte_id,
        int $volume_stockage,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::PAC_DOUBLE_SERVICE,
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
            generateur_mixte_id: $generateur_mixte_id,
            volume_stockage: $volume_stockage,
        );
    }

    public function build_chauffe_eau_thermodynamique(
        TypeGenerateur\TypeChauffeEauThermodynamique $type,
        Signaletique\Thermodynamique $signaletique,
        int $volume_stockage,
    ): Generateur {
        return $this->build(
            type: $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
            volume_stockage: $volume_stockage,
        );
    }

    public function build_chauffe_eau_electrique(
        TypeGenerateur\TypeChauffeEauElectrique $type,
        Signaletique\Electrique $signaletique,
        int $volume_stockage,
    ): Generateur {
        return $this->build(
            type: $volume_stockage === 0 ? TypeGenerateur::CHAUFFE_EAU_INSTANTANE : $type->to(),
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
        );
    }

    public function build_chauffe_eau_electrique_instantane(Signaletique\Electrique $signaletique,): Generateur
    {
        return $this->build(
            type: TypeGenerateur::CHAUFFE_EAU_INSTANTANE,
            energie: EnergieGenerateur::ELECTRICITE,
            signaletique: $signaletique,
        );
    }

    public function build_chauffe_eau_instantane(
        EnergieGenerateur\EnergieChauffeEauInstantane $energie,
        Signaletique\Combustion $signaletique,
    ): Generateur {
        return $this->build(
            type: TypeGenerateur::CHAUFFE_EAU_INSTANTANE,
            energie: $energie->to(),
            signaletique: $signaletique,
        );
    }

    public function build_accumulateur(
        TypeGenerateur\TypeAccumulateur $type,
        EnergieGenerateur\EnergieAccumulateur $energie,
        Signaletique\Combustion $signaletique,
        int $volume_stockage,
    ): Generateur {
        return $this->build(
            type: $volume_stockage === 0 ? TypeGenerateur::CHAUFFE_EAU_INSTANTANE : $type->to(),
            energie: $energie->to(),
            signaletique: $signaletique,
            volume_stockage: $volume_stockage,
        );
    }

    public function build_reseau_chaleur(?Id $reseau_chaleur_id): Generateur
    {
        return $this->build(
            type: TypeGenerateur::RESEAU_CHALEUR,
            energie: EnergieGenerateur::RESEAU_CHALEUR,
            reseau_chaleur_id: $reseau_chaleur_id,
            position_volume_chauffe: false,
            generateur_collectif: true,
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
}
