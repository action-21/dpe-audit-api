<?php

namespace App\Database\Local\Chauffage;

use App\Database\Local\{XMLTableElement, XMLTableRepositoryTrait};
use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Chauffage\Enum\{EquipementIntermittence, TypeChauffage, TypeEmission, TypeInstallation, TypeRegulation};
use App\Domain\Chauffage\Table\{I0, I0Repository};
use App\Domain\Enveloppe\Enum\ClasseInertie;

final class XMLI0Repository implements I0Repository
{
    use XMLTableRepositoryTrait;

    public static function table(): string
    {
        return 'chauffage.i0.xml';
    }

    public function find(int $id): ?I0
    {
        return ($record = $this->createQuery()->and(\sprintf('@id = "%s"', $id))->getOne()) ? $this->to($record) : null;
    }

    public function find_by(
        TypeBatiment $type_batiment,
        TypeInstallation $type_installation,
        TypeChauffage $type_chauffage,
        EquipementIntermittence $equipement_intermittence,
        TypeRegulation $type_regulation,
        TypeEmission $type_emission,
        ?ClasseInertie $inertie,
        ?bool $comptage_individuel,
    ): ?I0 {
        $record = $this->createQuery()
            ->and(\sprintf('type_batiment_id = "%s"', $type_batiment->id()))
            ->and(\sprintf('type_chauffage_id = "%s"', $type_chauffage->id()))
            ->and(\sprintf('equipement_intermittence_id = "%s"', $equipement_intermittence->id()))
            ->and(\sprintf('type_regulation_id = "%s"', $type_regulation->id()))
            ->and(\sprintf('type_emission_categorie_id = "%s"', $type_emission->categorie_id()))
            ->and(\sprintf('installation_collective = "%s" or installation_collective = ""', (int) $type_installation->installation_collective()))
            ->and(\sprintf('inertie_lourde = "%s" or inertie_lourde = ""', $inertie ? (int) $inertie->lourde() : null))
            ->and(\sprintf('comptage_individuel = "%s" or comptage_individuel = ""', null !== $comptage_individuel ? (int) $comptage_individuel : null))
            ->getOne();
        return $record ? $this->to($record) : null;
    }

    public function search_by(
        ?TypeBatiment $type_batiment = null,
        ?TypeInstallation $type_installation = null,
        ?TypeChauffage $type_chauffage = null,
        ?EquipementIntermittence $equipement_intermittence = null,
        ?TypeRegulation $type_regulation = null,
        ?TypeEmission $type_emission = null,
        ?ClasseInertie $inertie = null,
        ?bool $comptage_individuel = null,
        ?int $tv_i0_id = null
    ): array {
        $this->createQuery();

        if ($type_batiment) {
            $this->and(\sprintf('type_batiment_id = "%s"', $type_batiment->id()));
        }
        if ($type_installation) {
            $this->and(\sprintf('type_installation_id = "%s"', $type_installation->id()));
        }
        if ($type_chauffage) {
            $this->and(\sprintf('type_chauffage_id = "%s"', $type_chauffage->id()));
        }
        if ($equipement_intermittence) {
            $this->and(\sprintf('equipement_intermittence_id = "%s"', $equipement_intermittence->id()));
        }
        if ($type_regulation) {
            $this->and(\sprintf('type_regulation_id = "%s"', $type_regulation->id()));
        }
        if ($type_emission) {
            $this->and(\sprintf('type_emission_id = "%s"', $type_emission->id()));
        }
        if ($inertie) {
            $this->and(\sprintf('inertie_lourde = "%s" or inertie_lourde = ""', $inertie->lourde()));
        }
        if (null !== $comptage_individuel) {
            $this->and(\sprintf('comptage_individuel = "%s" or comptage_individuel = ""', (int) $comptage_individuel));
        }
        if ($tv_i0_id) {
            $this->and(\sprintf('tv_i0_id = "%s"', $tv_i0_id));
        }
        return \array_map(fn (XMLTableElement $record) => $this->to($record), $this->getMany());
    }

    public function to(XMLTableElement $record): I0
    {
        return new I0(
            id: $record->id(),
            i0: (float) $record->i0,
            tv_intermittence_id: (int) $record->tv_intermittence_id,
            comptage_individuel: ($value = $record->comptage_individuel) ? (bool) $value : null,
        );
    }
}
