<?php

namespace App\Domain\Ecs\Factory;

use App\Domain\Common\ValueObject\{Annee, Id};
use App\Domain\Ecs\Ecs;
use App\Domain\Ecs\Data\GenerateurData;
use App\Domain\Ecs\Entity\Generateur;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, UsageEcs};
use App\Domain\Ecs\Repository\ReseauChaleurRepository;
use App\Domain\Ecs\ValueObject\Generateur\{Position, Signaletique};
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Webmozart\Assert\Assert;

#[AutoconfigureTag('app.ecs.generateur.factory')]
abstract class GenerateurFactory
{
    protected Id $id;
    protected Ecs $ecs;
    protected string $description;
    protected TypeGenerateur $type;
    protected EnergieGenerateur $energie;
    protected ?Annee $annee_installation = null;
    protected ?Position $position = null;
    protected ?Signaletique $signaletique = null;

    public function __construct(protected readonly ReseauChaleurRepository $reseau_chaleur_repository) {}

    public function initialize(
        Id $id,
        Ecs $ecs,
        string $description,
        TypeGenerateur $type,
        EnergieGenerateur $energie,
        ?Annee $annee_installation,
    ): static {
        $this->id = $id;
        $this->ecs = $ecs;
        $this->description = $description;
        $this->type = $type;
        $this->energie = $energie;
        $this->annee_installation = $annee_installation;
        return $this;
    }

    public function set_position(
        bool $generateur_collectif,
        bool $position_volume_chauffe,
        bool $generateur_multi_batiment,
        ?Id $generateur_mixte_id,
    ): static {
        $this->position = Position::create(
            generateur_collectif: $generateur_collectif,
            position_volume_chauffe: $position_volume_chauffe,
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
            generateur_collectif: true,
            position_volume_chauffe: false,
            generateur_multi_batiment: true,
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
            ecs: $this->ecs,
            description: $this->description,
            type: $this->type,
            energie: $this->energie,
            usage: $this->position->generateur_mixte_id ? UsageEcs::CHAUFFAGE_ECS : UsageEcs::ECS,
            annee_installation: $this->annee_installation,
            position: $this->position,
            signaletique: $this->signaletique,
            data: GenerateurData::create()
        );
    }

    abstract public static function supports(TypeGenerateur $type, EnergieGenerateur $energie): bool;
}
