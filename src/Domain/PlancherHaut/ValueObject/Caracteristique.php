<?php

namespace App\Domain\PlancherHaut\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\PlancherHaut\Enum\{Inertie, TypePlancherHaut};
use App\Domain\PlancherHaut\PlancherHaut;

final class Caracteristique
{
    public function __construct(
        public readonly TypePlancherHaut $type,
        public readonly Inertie $inertie,
        public readonly float $surface,
        public readonly ?int $annee_construction,
        public readonly ?int $annee_renovation,
        public readonly ?float $u0,
        public readonly ?float $u,
    ) {}

    public static function create(
        TypePlancherHaut $type,
        Inertie $inertie,
        float $surface,
        ?int $annee_construction,
        ?int $annee_renovation,
        ?float $u0,
        ?float $u,
    ): self {
        return new self(
            type: $type,
            inertie: $inertie,
            surface: $surface,
            annee_construction: $annee_construction,
            annee_renovation: $annee_renovation,
            u0: $u0,
            u: $u,
        );
    }

    public function controle(PlancherHaut $entity): void
    {
        Assert::positif($this->surface);
        Assert::annee($this->annee_construction);
        Assert::annee($this->annee_renovation);
        Assert::positif($this->u0);
        Assert::positif($this->u);
        Assert::superieur_ou_egal_a($this->annee_construction, $entity->annee_construction_defaut());
        Assert::superieur_ou_egal_a($this->annee_renovation, $entity->annee_construction_defaut());
    }
}
