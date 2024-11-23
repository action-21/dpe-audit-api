<?php

namespace App\Domain\Ecs\Entity;

use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Enum\{CategorieGenerateur, EnergieGenerateur, TypeGenerateur, UsageEcs};
use App\Domain\Ecs\Service\{MoteurPerformance, MoteurPerte};
use App\Domain\Ecs\ValueObject\{Performance, PerteCollection, Signaletique};
use App\Domain\Simulation\Simulation;

final class Generateur
{
    private CategorieGenerateur $categorie;

    private ?Performance $performance = null;
    private ?PerteCollection $pertes_generation = null;
    private ?PerteCollection $pertes_stockage = null;

    public function __construct(
        private readonly Id $id,
        private readonly Ecs $ecs,
        private ?Id $generateur_mixte_id,
        private ?Id $reseau_chaleur_id,
        private string $description,
        private TypeGenerateur $type,
        private EnergieGenerateur $energie,
        private int $volume_stockage,
        private bool $position_volume_chauffe,
        private bool $generateur_collectif,
        private Signaletique $signaletique,
        private ?int $annee_installation,
    ) {}

    private function pre_update(bool $dereference_generateur_mixte = true, bool $dereference_reseau_chaleur = true): void
    {
        $this->signaletique = new Signaletique();
        $this->volume_stockage = 0;
        $this->generateur_mixte_id = $dereference_generateur_mixte ? null : $this->generateur_mixte_id;
        $this->reseau_chaleur_id = $dereference_reseau_chaleur ? null : $this->reseau_chaleur_id;
    }

    private function post_update(): void
    {
        $this->determine_categorie();
        $this->reinitialise();
        $this->controle();
    }

    public function set_chaudiere(
        TypeGenerateur\TypeChaudiere $type,
        EnergieGenerateur\EnergieChaudiere $energie,
        Signaletique\Chaudiere $signaletique,
        int $volume_stockage,
    ): self {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = $type->to();
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->volume_stockage = $volume_stockage;
        $this->post_update();
        return $this;
    }

    public function set_chaudiere_electrique(
        Signaletique\ChaudiereElectrique $signaletique,
        int $volume_stockage,
    ): self {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::CHAUDIERE_STANDARD;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->volume_stockage = $volume_stockage;
        $this->post_update();
        return $this;
    }

    public function set_chaudiere_multi_batiment(EnergieGenerateur\EnergieChaudiereMultiBatiment $energie,): self
    {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::CHAUDIERE_MULTI_BATIMENT;
        $this->energie = $energie->to();
        $this->generateur_collectif = true;
        $this->post_update();
        return $this;
    }

    public function set_poele_bouilleur(
        EnergieGenerateur\EnergiePoeleBouilleur $energie,
        Signaletique\Combustion $signaletique,
        int $volume_stockage,
    ): self {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::POELE_BOUILLEUR;
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->volume_stockage = $volume_stockage;
        $this->post_update();
        return $this;
    }

    public function set_pac_multi_batiment(): self
    {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::PAC_MULTI_BATIMENT;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->generateur_collectif = true;
        $this->post_update();
        return $this;
    }

    public function set_pac_double_service(
        Signaletique\Thermodynamique $signaletique,
        int $volume_stockage,
    ): self {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::PAC_DOUBLE_SERVICE;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->volume_stockage = $volume_stockage;
        $this->post_update();
        return $this;
    }

    public function set_chauffe_eau_thermodynamique(
        TypeGenerateur\TypeChauffeEauThermodynamique $type,
        Signaletique\Thermodynamique $signaletique,
        int $volume_stockage,
    ): self {
        $this->pre_update();
        $this->type = $type->to();
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->volume_stockage = $volume_stockage;
        $this->post_update();
        return $this;
    }

    public function set_chauffe_eau_electrique(
        TypeGenerateur\TypeChauffeEauElectrique $type,
        Signaletique\Electrique $signaletique,
        int $volume_stockage,
    ): self {
        $this->pre_update();
        $this->type = $volume_stockage === 0 ? TypeGenerateur::CHAUFFE_EAU_INSTANTANE : $type->to();
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->volume_stockage = $volume_stockage;
        $this->post_update();
        return $this;
    }

    public function set_chauffe_eau_electrique_instantane(Signaletique\Electrique $signaletique,): self
    {
        $this->pre_update();
        $this->type = TypeGenerateur::CHAUFFE_EAU_INSTANTANE;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->post_update();
        return $this;
    }

    public function set_chauffe_eau_instantane(
        EnergieGenerateur\EnergieChauffeEauInstantane $energie,
        Signaletique\Combustion $signaletique,
    ): self {
        $this->pre_update();
        $this->type = TypeGenerateur::CHAUFFE_EAU_INSTANTANE;
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->post_update();
        return $this;
    }

    public function set_accumulateur(
        TypeGenerateur\TypeAccumulateur $type,
        EnergieGenerateur\EnergieAccumulateur $energie,
        Signaletique\Combustion $signaletique,
        int $volume_stockage,
    ): self {
        $this->pre_update();
        $this->type = $volume_stockage === 0 ? TypeGenerateur::CHAUFFE_EAU_INSTANTANE : $type->to();
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->volume_stockage = $volume_stockage;
        $this->post_update();
        return $this;
    }

    public function set_reseau_chaleur(): self
    {
        $this->pre_update();
        $this->type = TypeGenerateur::RESEAU_CHALEUR;
        $this->energie = EnergieGenerateur::RESEAU_CHALEUR;
        $this->generateur_collectif = true;
        $this->post_update();
        return $this;
    }

    public function set_systeme_collectif_defaut(): self
    {
        $this->pre_update();
        $this->type = TypeGenerateur::SYSTEME_COLLECTIF_PAR_DEFAUT;
        $this->energie = EnergieGenerateur::FIOUL;

        $this->generateur_collectif = true;
        $this->post_update();
        return $this;
    }

    public function update(
        string $description,
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        ?int $annee_installation,
    ): self {
        $this->description = $description;
        $this->annee_installation = $annee_installation;

        $this->position_volume_chauffe = match (true) {
            $this->categorie === CategorieGenerateur::CHAUDIERE_MULTI_BATIMENT => false,
            $this->categorie === CategorieGenerateur::PAC_MULTI_BATIMENT => false,
            $this->categorie === CategorieGenerateur::RESEAU_CHALEUR => false,
            default => $position_volume_chauffe,
        };
        $this->generateur_collectif = match (true) {
            $this->categorie === CategorieGenerateur::CHAUDIERE_MULTI_BATIMENT => true,
            $this->categorie === CategorieGenerateur::PAC_MULTI_BATIMENT => true,
            $this->categorie === CategorieGenerateur::RESEAU_CHALEUR => true,
            $this->type === TypeGenerateur::SYSTEME_COLLECTIF_PAR_DEFAUT => true,
            default => $generateur_collectif,
        };

        $this->post_update();
        return $this;
    }

    public function determine_categorie(): self
    {
        $this->categorie = CategorieGenerateur::determine(type: $this->type, energie: $this->energie);
        return $this;
    }

    public function controle(): void
    {
        Assert::positif_ou_zero($this->volume_stockage);
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $this->ecs->audit()->annee_construction_batiment());
        $this->signaletique?->controle();
    }

    public function reinitialise(): void
    {
        $this->performance = null;
        $this->pertes_generation = null;
        $this->pertes_stockage = null;
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        $this->performance = $moteur->calcule_performance($this, $simulation);
        return $this;
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        $this->pertes_generation = $moteur->calcule_pertes_generation($this, $simulation);
        $this->pertes_stockage = $moteur->calcule_pertes_stockage_generateur($this, $simulation);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function ecs(): Ecs
    {
        return $this->ecs;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function categorie(): CategorieGenerateur
    {
        return $this->categorie;
    }

    public function type(): TypeGenerateur
    {
        return $this->type;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->energie;
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }

    public function usage(): UsageEcs
    {
        return $this->generateur_mixte_id ? UsageEcs::CHAUFFAGE_ECS : UsageEcs::ECS;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
    }

    public function generateur_collectif(): bool
    {
        return $this->generateur_collectif;
    }

    public function volume_stockage(): int
    {
        return $this->volume_stockage;
    }

    public function reseau_chaleur_id(): ?Id
    {
        return $this->reseau_chaleur_id;
    }

    public function reference_reseau_chaleur(Id $reseau_chaleur_id): self
    {
        if ($this->categorie === CategorieGenerateur::RESEAU_CHALEUR) {
            $this->reseau_chaleur_id = $reseau_chaleur_id;
            $this->reinitialise();
        }
        return $this;
    }

    public function dereference_reseau_chaleur(): self
    {
        $this->reseau_chaleur_id = null;
        $this->reinitialise();
        return $this;
    }

    public function generateur_mixte_id(): ?Id
    {
        return $this->generateur_mixte_id;
    }

    public function reference_generateur_mixte(Id $generateur_mixte_id): self
    {
        if (false === \in_array($this->categorie, [
            CategorieGenerateur::ACCUMULATEUR,
            CategorieGenerateur::CHAUFFE_EAU_ELECTRIQUE,
            CategorieGenerateur::CHAUFFE_EAU_INSTANTANE,
            CategorieGenerateur::CHAUFFE_EAU_THERMODYNAMIQUE,
        ])) $this->generateur_mixte_id = $generateur_mixte_id;

        $this->reinitialise();
        return $this;
    }

    public function dereference_generateur_mixte(): self
    {
        $this->generateur_mixte_id = null;
        $this->reinitialise();
        return $this;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function pertes_generation(): ?PerteCollection
    {
        return $this->pertes_generation;
    }

    public function pertes_stockage(): ?PerteCollection
    {
        return $this->pertes_stockage;
    }
}
