<?php

namespace App\Domain\PlancherBas\ValueObject;

use App\Domain\Common\Service\Assert;
use App\Domain\PlancherBas\Enum\{Inertie, TypePlancherBas};
use App\Domain\PlancherBas\PlancherBas;

final class Caracteristique
{
    public function __construct(
        public readonly TypePlancherBas $type,
        public readonly Inertie $inertie,
        public readonly float $perimetre,
        public readonly float $surface,
        public readonly ?int $annee_construction,
        public readonly ?int $annee_renovation,
        public readonly ?float $u0,
        public readonly ?float $u,
    ) {}

    public static function create(
        TypePlancherBas $type,
        Inertie $inertie,
        float $surface,
        float $perimetre,
        ?int $annee_construction,
        ?int $annee_renovation,
        ?float $u0,
        ?float $u,
    ): self {
        return new self(
            type: $type,
            inertie: $inertie,
            surface: $surface,
            perimetre: $perimetre,
            annee_construction: $annee_construction,
            annee_renovation: $annee_renovation,
            u0: $u0,
            u: $u,
        );
    }

    public function controle(PlancherBas $entity): void
    {
        Assert::positif($this->surface);
        Assert::positif($this->perimetre);
        Assert::annee($this->annee_construction);
        Assert::annee($this->annee_renovation);
        Assert::positif($this->u0);
        Assert::positif($this->u);
        Assert::superieur_ou_egal_a($this->annee_construction, $entity->enveloppe()->annee_construction_batiment());
        Assert::superieur_ou_egal_a($this->annee_renovation, $entity->enveloppe()->annee_construction_batiment());
    }
}
