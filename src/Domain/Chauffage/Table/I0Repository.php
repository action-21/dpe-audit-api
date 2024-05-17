<?php

namespace App\Domain\Chauffage\Table;

use App\Domain\Batiment\Enum\TypeBatiment;
use App\Domain\Chauffage\Enum\{EquipementIntermittence, TypeChauffage, TypeEmission, TypeInstallation, TypeRegulation};
use App\Domain\Enveloppe\Enum\ClasseInertie;

interface I0Repository
{
    public function find(int $id): ?I0;

    public function find_by(
        TypeBatiment $type_batiment,
        TypeInstallation $type_installation,
        TypeChauffage $type_chauffage,
        EquipementIntermittence $equipement_intermittence,
        TypeRegulation $type_regulation,
        TypeEmission $type_emission,
        ?ClasseInertie $inertie,
        ?bool $comptage_individuel,
    ): ?I0;

    /** @return array<I0> */
    public function search_by(
        ?TypeBatiment $type_batiment = null,
        ?TypeInstallation $type_installation = null,
        ?TypeChauffage $type_chauffage = null,
        ?EquipementIntermittence $equipement_intermittence = null,
        ?TypeRegulation $type_regulation = null,
        ?TypeEmission $type_emission = null,
        ?ClasseInertie $inertie = null,
        ?bool $comptage_individuel = null,
        ?int $tv_i0_id = null,
    ): array;
}
