<?php

namespace App\Domain\Chauffage\Entity;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Enum\{CategorieGenerateur, EnergieGenerateur, TypeChaudiere, TypeGenerateur, UsageChauffage};
use App\Domain\Chauffage\Service\{MoteurDimensionnement, MoteurPerformance, MoteurPerte};
use App\Domain\Chauffage\ValueObject\{Performance, PerteCollection, Signaletique};
use App\Domain\Common\Service\Assert;
use App\Domain\Common\Type\Id;
use App\Domain\Simulation\Simulation;

final class Generateur
{
    private CategorieGenerateur $categorie;

    private ?float $pch = null;
    private ?Performance $performance = null;
    private ?PerteCollection $pertes_generation = null;

    public function __construct(
        private readonly Id $id,
        private readonly Chauffage $chauffage,
        private ?Id $generateur_mixte_id,
        private ?Id $reseau_chaleur_id,
        private string $description,
        private TypeGenerateur $type,
        private EnergieGenerateur $energie,
        private bool $position_volume_chauffe,
        private bool $generateur_collectif,
        private Signaletique $signaletique,
        private ?int $annee_installation,
        private ?TypeGenerateur $type_partie_chaudiere,
        private ?EnergieGenerateur $energie_partie_chaudiere,
    ) {}

    private function pre_update(bool $dereference_generateur_mixte = true, bool $dereference_reseau_chaleur = true): void
    {
        $this->type_partie_chaudiere = null;
        $this->energie_partie_chaudiere = null;
        $this->signaletique = new Signaletique();
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
        bool $generateur_collectif,
    ): self {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = $type->to();
        $this->energie_partie_chaudiere = $energie->to();
        $this->signaletique = $signaletique;
        $this->generateur_collectif = $generateur_collectif;
        $this->post_update();
        return $this;
    }

    public function set_chaudiere_electrique(
        Signaletique\ChaudiereElectrique $signaletique,
        bool $generateur_collectif,
    ): self {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::CHAUDIERE_STANDARD;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->generateur_collectif = $generateur_collectif;
        $this->post_update();
        return $this;
    }

    public function set_chaudiere_multi_batiment(EnergieGenerateur\EnergieChaudiere $energie,): self
    {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::CHAUDIERE_MULTI_BATIMENT;
        $this->energie = $energie->to();
        $this->signaletique = new Signaletique\Chaudiere(type_chaudiere: TypeChaudiere::CHAUDIERE_SOL);
        $this->generateur_collectif = true;
        $this->post_update();
        return $this;
    }

    public function set_pac_multi_batiment(): self
    {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = TypeGenerateur::PAC_MULTI_BATIMENT;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = new Signaletique\Chaudiere(type_chaudiere: TypeChaudiere::CHAUDIERE_SOL);
        $this->generateur_collectif = true;
        $this->post_update();
        return $this;
    }

    public function set_pac(
        TypeGenerateur\TypePac $type,
        Signaletique\Pac $signaletique,
        bool $generateur_collectif,
    ): self {
        $this->pre_update(dereference_generateur_mixte: $type->to() === TypeGenerateur::PAC_AIR_AIR);
        $this->type = $type->to();
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->generateur_collectif = $generateur_collectif;
        $this->post_update();
        return $this;
    }

    public function set_pac_hybride(
        TypeGenerateur\TypePacHybride $type,
        TypeGenerateur\TypeChaudiere $type_partie_chaudiere,
        EnergieGenerateur\EnergieChaudiere $energie_partie_chaudiere,
        Signaletique\PacHybride $signaletique,
        bool $generateur_collectif,
    ): self {
        $this->pre_update(dereference_generateur_mixte: false);
        $this->type = $type->to();
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->type_partie_chaudiere = $type_partie_chaudiere->to();
        $this->energie_partie_chaudiere = $energie_partie_chaudiere->to();
        $this->generateur_collectif = $generateur_collectif;
        $this->post_update();
        return $this;
    }

    public function set_generateur_air_chaud_combustion(
        TypeGenerateur\TypeGenerateurAirChaud $type,
        EnergieGenerateur\EnergieGenerateurAirChaud $energie,
        Signaletique\Combustion $signaletique,
        bool $generateur_collectif,
    ): self {
        $this->pre_update();
        $this->type = $type->to();
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->generateur_collectif = $generateur_collectif;
        $this->post_update();
        return $this;
    }

    public function set_generateur_air_chaud_electrique(?float $pn,): self
    {
        $this->pre_update();
        $this->type = TypeGenerateur::GENERATEUR_AIR_CHAUD;
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = new Signaletique\ChauffageElectrique(pn: $pn);
        $this->generateur_collectif = false;
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

    public function set_poele_insert(
        TypeGenerateur\TypePoeleInsert $type,
        EnergieGenerateur\EnergiePoeleInsert $energie,
        Signaletique\Combustion $signaletique,
    ): self {
        $this->pre_update();
        $this->type = $type->to();
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->generateur_collectif = false;
        $this->post_update();
        return $this;
    }

    public function set_poele_insert_bois(
        TypeGenerateur\TypePoeleInsert $type,
        EnergieGenerateur\EnergiePoeleInsertBois $energie,
        Signaletique\PoeleInsertBois $signaletique,
    ): self {
        $this->pre_update();
        $this->type = $type->to();
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->generateur_collectif = false;
        $this->post_update();
        return $this;
    }

    public function set_poele_bois_bouilleur(
        EnergieGenerateur\EnergiePoeleBouilleur $energie,
        Signaletique\Combustion $signaletique,
        bool $generateur_collectif,
    ): self {
        $this->pre_update();
        $this->type = TypeGenerateur::POELE_BOUILLEUR;
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->generateur_collectif = $generateur_collectif;
        $this->post_update();
        return $this;
    }

    public function set_chauffage_electrique(
        TypeGenerateur\TypeChauffageElectrique $type,
        Signaletique\ChauffageElectrique $signaletique,
    ): self {
        $this->pre_update();
        $this->type = $type->to();
        $this->energie = EnergieGenerateur::ELECTRICITE;
        $this->signaletique = $signaletique;
        $this->generateur_collectif = false;
        $this->post_update();
        return $this;
    }

    public function set_radiateur_independant(
        EnergieGenerateur\EnergieRadiateurIndependant $energie,
        Signaletique\Combustion $signaletique,
    ): self {
        $this->pre_update();
        $this->type = TypeGenerateur::RADIATEUR_INDEPENDANT;
        $this->energie = $energie->to();
        $this->signaletique = $signaletique;
        $this->generateur_collectif = false;
        $this->post_update();
        return $this;
    }

    public function set_reseau_chaleur(): self
    {
        $this->pre_update(dereference_reseau_chaleur: false);
        $this->type = TypeGenerateur::RESEAU_CHALEUR;
        $this->energie = EnergieGenerateur::RESEAU_CHALEUR;
        $this->signaletique = null;
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

    public function controle(): void
    {
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $this->chauffage->annee_construction_batiment());
    }

    public function reinitialise(): void
    {
        $this->pch = null;
        $this->performance = null;
        $this->pertes_generation = null;
    }

    public function determine_categorie(): self
    {
        $this->categorie = CategorieGenerateur::determine(type_generateur: $this->type, energie_generateur: $this->energie);
        return $this;
    }

    public function calcule_dimensionnement(MoteurDimensionnement $moteur, Simulation $simulation): self
    {
        $this->pch = $moteur->calcule_pch($this, $simulation);
        return $this;
    }

    public function calcule_performance(MoteurPerformance $moteur, Simulation $simulation): self
    {
        $this->performance = $moteur->calcule_performance($this, $simulation);
        return $this;
    }

    public function calcule_pertes(MoteurPerte $moteur, Simulation $simulation): self
    {
        $this->pertes_generation = $moteur->calcule_pertes_generation($this, $simulation);
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function chauffage(): Chauffage
    {
        return $this->chauffage;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function categorie(): CategorieGenerateur
    {
        return $this->categorie;
    }

    public function effet_joule(): bool
    {
        return $this->categorie->effet_joule();
    }

    public function usage(): UsageChauffage
    {
        return $this->generateur_mixte_id ? UsageChauffage::CHAUFFAGE_ECS : UsageChauffage::CHAUFFAGE;;
    }

    public function type(): TypeGenerateur
    {
        return $this->type;
    }

    public function energie(): EnergieGenerateur
    {
        return $this->energie;
    }

    public function type_partie_chaudiere(): ?TypeGenerateur
    {
        return $this->type_partie_chaudiere;
    }

    public function energie_partie_chaudiere(): ?EnergieGenerateur
    {
        return $this->energie_partie_chaudiere;
    }

    public function signaletique(): Signaletique
    {
        return $this->signaletique;
    }

    public function annee_installation(): ?int
    {
        return $this->annee_installation;
    }

    public function generateur_collectif(): bool
    {
        return $this->generateur_collectif;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->position_volume_chauffe;
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
        return $this;
    }

    public function generateur_mixte_id(): ?Id
    {
        return $this->generateur_mixte_id;
    }

    public function reference_generateur_mixte(Id $generateur_mixte_id): self
    {
        if ($this->type->usage_mixte()) {
            $this->generateur_mixte_id = $generateur_mixte_id;
            $this->reinitialise();
        }
        return $this;
    }

    public function dereference_generateur_mixte(): self
    {
        $this->generateur_mixte_id = null;
        $this->reinitialise();
        return $this;
    }

    public function pch(): ?float
    {
        return $this->pch;
    }

    public function performance(): ?Performance
    {
        return $this->performance;
    }

    public function pertes_generation(): ?PerteCollection
    {
        return $this->pertes_generation;
    }
}
