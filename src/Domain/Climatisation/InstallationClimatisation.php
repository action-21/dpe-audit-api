<?php

namespace App\Domain\Climatisation;

use App\Domain\Batiment\Batiment;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Climatisation\Enum\{EnergieGenerateur, TypeGenerateur};
use App\Domain\Climatisation\ValueObject\{AnneeInstallation, Seer};

final class InstallationClimatisation
{
    public function __construct(
        private readonly Id $id,
        private readonly Batiment $batiment,
        private string $description,
        private TypeGenerateur $type_generateur,
        private ?AnneeInstallation $annee_installation = null,
        private ?EnergieGenerateur $energie = null,
        private ?Seer $seer = null,
    ) {
    }

    public static function create(
        Batiment $batiment,
        string $description,
        TypeGenerateur $type_generateur,
        ?AnneeInstallation $annee_installation = null,
        ?EnergieGenerateur $energie = null,
        ?Seer $seer = null,
    ): self {
        $entity = new self(
            id: Id::create(),
            batiment: $batiment,
            description: $description,
            type_generateur: $type_generateur,
            annee_installation: $annee_installation,
            energie: $energie,
            seer: $seer,
        );

        $entity->controle_coherence();
        return $entity;
    }

    public function update(
        string $description,
        TypeGenerateur $type_generateur,
        ?AnneeInstallation $annee_installation = null,
        ?EnergieGenerateur $energie = null,
        ?Seer $seer = null,
    ): self {
        $this->description = $description;
        $this->type_generateur = $type_generateur;
        $this->annee_installation = $annee_installation;
        $this->energie = $energie;
        $this->seer = $seer;
        $this->controle_coherence();
        return $this;
    }

    private function controle_coherence(): void
    {
        $cases_energie = $this->energie ? EnergieGenerateur::cases_by_type_generateur($this->type_generateur) : [];

        if ($this->energie && \count($cases_energie) === 1) {
            $this->energie = \reset($cases_energie);
        }
        if ($this->energie && !\in_array($this->energie, $cases_energie)) {
            throw new \InvalidArgumentException("L'énergie {$this->energie->lib()} n'est pas applicable pour ce type de générateur");
        }
        if ($this->annee_installation->valeur() < $this->batiment->annee_construction()->valeur()) {
            throw new \InvalidArgumentException("La période d'installation est antérieure à la période de construction du bâtiment");
        }
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

    public function type_generateur(): TypeGenerateur
    {
        return $this->type_generateur;
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return $this->annee_installation;
    }

    public function energie(): ?EnergieGenerateur
    {
        return $this->energie;
    }

    public function seer(): ?Seer
    {
        return $this->seer;
    }
}
