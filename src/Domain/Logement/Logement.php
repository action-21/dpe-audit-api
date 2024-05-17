<?php

namespace App\Domain\Logement;

use App\Domain\Batiment\Batiment;
use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Logement\Entity\{Climatisation, ClimatisationCollection, Etage, EtageCollection, Ventilation, VentilationCollection};
use App\Domain\Logement\Enum\{PositionImmeuble, TypologieLogement};

final class Logement
{
    public function __construct(
        private readonly Id $id,
        private readonly Batiment $batiment,
        private string $description,
        private ?PositionImmeuble $position,
        private ?TypologieLogement $typologie,
        private EtageCollection $etage_collection,
        private VentilationCollection $ventilation_collection,
        private ClimatisationCollection $climatisation_collection,
    ) {
    }

    public static function create_maison(
        Batiment $batiment,
        string $description,
        ?TypologieLogement $typologie = null
    ): self {
        if ($batiment->type_batiment() !== TypeBatiment::MAISON) {
            throw new \RuntimeException('Le type de bâtiment n\'est pas une maison');
        }
        return new self(
            id: Id::create(),
            batiment: $batiment,
            description: $description,
            typologie: $typologie,
            position: null,
            etage_collection: new EtageCollection,
            ventilation_collection: new VentilationCollection,
            climatisation_collection: new ClimatisationCollection,
        );
    }

    public static function create_appartement(
        Batiment $batiment,
        string $description,
        TypologieLogement $typologie,
        PositionImmeuble $position_immeuble,
    ): self {
        if ($batiment->type_batiment() !== TypeBatiment::IMMEUBLE) {
            throw new \RuntimeException('Le type de bâtiment n\'est pas un immeuble');
        }
        return new self(
            id: Id::create(),
            batiment: $batiment,
            description: $description,
            typologie: $typologie,
            position: $position_immeuble,
            etage_collection: new EtageCollection,
            ventilation_collection: new VentilationCollection,
            climatisation_collection: new ClimatisationCollection,
        );
    }

    public function update(
        string $description,
        ?TypologieLogement $typologie = null,
        ?PositionImmeuble $position = null
    ): self {
        if ($this->batiment->type_batiment() === TypeBatiment::IMMEUBLE) {
            $this->description = $description;
            $this->typologie = $typologie ?? $this->typologie;
            $this->position = $position ?? $this->position;
            return $this;
        }
        $this->typologie = $typologie;
        $this->description = $description;
        return $this;
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function batiment(): Batiment
    {
        return $this->batiment;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function surface_habitable(): float
    {
        return $this->etage_collection->surface_habitable();
    }

    public function hauteur_sous_plafond(): float
    {
        return $this->etage_collection->hauteur_sous_plafond();
    }

    public function typologie(): ?TypologieLogement
    {
        return $this->typologie;
    }

    public function position(): ?PositionImmeuble
    {
        return $this->position;
    }

    public function etage_collection(): EtageCollection
    {
        return $this->etage_collection;
    }

    public function get_etage(Id $id): ?Etage
    {
        return $this->etage_collection->find($id);
    }

    public function add_etage(Etage $entity): self
    {
        $this->etage_collection->add($entity);
        return $this;
    }

    public function remove_etage(Etage $entity): self
    {
        $this->etage_collection->removeElement($entity);
        return $this;
    }

    public function ventilation_collection(): VentilationCollection
    {
        return $this->ventilation_collection;
    }

    public function get_ventilation(Id $id): ?Ventilation
    {
        return $this->ventilation_collection->find($id);
    }

    public function add_ventilation(Ventilation $entity): self
    {
        $this->ventilation_collection->add($entity);
        return $this;
    }

    public function remove_ventilation(Ventilation $entity): self
    {
        $this->ventilation_collection->removeElement($entity);
        return $this;
    }

    /*
    public function chauffage_collection(): InstallationChauffageCollection
    {
        return $this->chauffage_collection;
    }

    public function get_chauffage(Id $id): ?InstallationChauffage
    {
        return $this->chauffage_collection->find($id);
    }

    public function add_chauffage(InstallationChauffage $entity): self
    {
        $this->chauffage_collection->add($entity);
        return $this;
    }

    public function remove_chauffage(InstallationChauffage $entity): self
    {
        $this->chauffage_collection->removeElement($entity);
        return $this;
    }

    public function ecs_collection(): InstallationEcsCollection
    {
        return $this->ecs_collection;
    }

    public function get_ecs(Id $id): ?InstallationEcs
    {
        return $this->ecs_collection->find($id);
    }

    public function add_ecs(InstallationEcs $entity): self
    {
        $this->ecs_collection->add($entity);
        return $this;
    }

    public function remove_ecs(InstallationEcs $entity): self
    {
        $this->ecs_collection->removeElement($entity);
        return $this;
    }*/

    public function climatisation_collection(): ClimatisationCollection
    {
        return $this->climatisation_collection;
    }

    public function get_climatisation(Id $id): ?Climatisation
    {
        return $this->climatisation_collection->find($id);
    }

    public function add_climatisation(Climatisation $entity): self
    {
        $this->climatisation_collection->add($entity);
        return $this;
    }

    public function remove_climatisation(Climatisation $entity): self
    {
        $this->climatisation_collection->removeElement($entity);
        return $this;
    }
}
