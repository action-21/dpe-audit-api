<?php

namespace App\Domain\Chauffage\Factory;

use App\Domain\Chauffage\Chauffage;
use App\Domain\Chauffage\Data\GenerateurData;
use App\Domain\Chauffage\Entity\Generateur;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, TypeGenerateur, UsageChauffage};
use App\Domain\Chauffage\Repository\ReseauChaleurRepository;
use App\Domain\Chauffage\ValueObject\Generateur\{Position, Signaletique};
use App\Domain\Common\ValueObject\{Annee, Id};
use Webmozart\Assert\Assert;

abstract class GenerateurFactory
{
    protected Id $id;
    protected Chauffage $chauffage;
    protected string $description;
    protected TypeGenerateur $type;
    protected EnergieGenerateur $energie;
    protected ?EnergieGenerateur $energie_partie_chaudiere = null;
    protected ?Annee $annee_installation = null;
    protected ?Position $position = null;
    protected ?Signaletique $signaletique = null;

    public function __construct(protected readonly ReseauChaleurRepository $reseau_chaleur_repository) {}

    public function initialize(
        Id $id,
        Chauffage $chauffage,
        string $description,
        TypeGenerateur $type,
        EnergieGenerateur $energie,
        ?Annee $annee_installation,
    ): static {
        $this->id = $id;
        $this->chauffage = $chauffage;
        $this->description = $description;
        $this->type = $type;
        $this->energie = $energie;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    public function set_energie_partie_chaudiere(EnergieGenerateur $energie_partie_chaudiere): static
    {
        $this->energie_partie_chaudiere = $energie_partie_chaudiere;
        return $this;
    }

    public function set_position(
        bool $position_volume_chauffe,
        bool $generateur_collectif,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id,
    ): static {
        $this->position = Position::create(
            position_volume_chauffe: $position_volume_chauffe,
            generateur_collectif: $generateur_collectif,
            generateur_multi_batiment: $generateur_multi_batiment,
            generateur_mixte_id: $generateur_mixte_id,
        );
        return $this;
    }

    public function set_reseau_chaleur(Id $id): static
    {
        Assert::notNull($this->position);
        Assert::notNull($entity = $this->reseau_chaleur_repository->find($id));

        $this->position = Position::create(
            generateur_collectif: $this->position->generateur_collectif,
            generateur_multi_batiment: $this->position->generateur_multi_batiment,
            position_volume_chauffe: $this->position->position_volume_chauffe,
            generateur_mixte_id: $this->position->generateur_mixte_id,
            reseau_chaleur: $entity,
        );
        return $this;
    }

    public function set_signaletique(Signaletique $signaletique): static
    {
        $this->signaletique = $signaletique;
        return $this;
    }

    public function build(): Generateur
    {
        Assert::notNull($this->position);
        Assert::notNull($this->signaletique);

        return new Generateur(
            id: $this->id,
            chauffage: $this->chauffage,
            description: $this->description,
            type: $this->type,
            energie: $this->energie,
            energie_partie_chaudiere: $this->energie_partie_chaudiere,
            usage: $this->position->generateur_mixte_id ? UsageChauffage::CHAUFFAGE_ECS : UsageChauffage::CHAUFFAGE,
            annee_installation: $this->annee_installation,
            position: $this->position,
            signaletique: $this->signaletique,
            data: GenerateurData::create()
        );
    }

    abstract public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool;
}
