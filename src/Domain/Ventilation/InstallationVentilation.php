<?php

namespace App\Domain\Ventilation;

use App\Domain\Batiment\Batiment;
use App\Domain\Common\ValueObject\Id;
use App\Domain\Ventilation\Enum\{TypeInstallation, TypeVentilation};
use App\Domain\Ventilation\ValueObject\{AnneeInstallation, Surface};

final class InstallationVentilation
{
    public function __construct(
        private readonly Id $id,
        private readonly Batiment $batiment,
        private string $description,
        private TypeVentilation $type_ventilation,
        private ?TypeInstallation $type_installation = null,
        private ?AnneeInstallation $annee_installation = null,
    ) {
    }

    public static function create_ventilation_naturelle(
        Batiment $batiment,
        string $description,
        TypeVentilation $type_ventilation,
    ): self {
        return (new self(
            id: Id::create(),
            batiment: $batiment,
            description: $description,
            type_ventilation: $type_ventilation,
        ))->set_ventilation_naturelle(
            type_ventilation: $type_ventilation
        );
    }

    public static function create_ventilation_mecanique(
        Batiment $batiment,
        string $description,
        TypeVentilation $type_ventilation,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        return (new self(
            id: Id::create(),
            batiment: $batiment,
            description: $description,
            type_ventilation: $type_ventilation,
            annee_installation: $annee_installation,
        ))->set_ventilation_mecanique(
            type_ventilation: $type_ventilation,
            annee_installation: $annee_installation
        );
    }

    public static function create_ventilation_double_flux(
        Batiment $batiment,
        string $description,
        TypeVentilation $type_ventilation,
        TypeInstallation $type_installation,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        return (new self(
            id: Id::create(),
            batiment: $batiment,
            description: $description,
            type_ventilation: $type_ventilation,
            type_installation: $type_installation,
            annee_installation: $annee_installation,
        ))->set_ventilation_double_flux(
            type_ventilation: $type_ventilation,
            type_installation: $type_installation,
            annee_installation: $annee_installation,
        );
    }

    public static function create_ventilation_hybride(
        Batiment $batiment,
        string $description,
        TypeVentilation $type_ventilation,
        TypeInstallation $type_installation,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        return (new self(
            id: Id::create(),
            batiment: $batiment,
            description: $description,
            type_ventilation: $type_ventilation,
            type_installation: $type_installation,
            annee_installation: $annee_installation,
        ))->set_ventilation_hybride(
            type_ventilation: $type_ventilation,
            type_installation: $type_installation,
            annee_installation: $annee_installation,
        );
    }

    public function update(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function set_ventilation_naturelle(TypeVentilation $type_ventilation): self
    {
        if (false === $type_ventilation->ventilation_naturelle()) {
            throw new \InvalidArgumentException("Le type de ventilation n'est pas naturelle");
        }
        $this->type_ventilation = $type_ventilation;
        $this->type_installation = null;
        $this->annee_installation = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_ventilation_mecanique(
        TypeVentilation $type_ventilation,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        if ($type_ventilation->ventilation_double_flux() || $type_ventilation->ventilation_hybride()) {
            throw new \DomainException("Méthode incompatible avec le type de ventilation double flux ou hybride");
        }
        $this->type_ventilation = $type_ventilation;
        $this->annee_installation = $annee_installation;
        $this->type_installation = null;
        $this->controle_coherence();
        return $this;
    }

    public function set_ventilation_double_flux(
        TypeVentilation $type_ventilation,
        TypeInstallation $type_installation,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        if (false === $type_ventilation->ventilation_double_flux()) {
            throw new \InvalidArgumentException("Le type de ventilation n'est pas double flux");
        }
        $this->type_ventilation = $type_ventilation;
        $this->type_installation = $type_installation;
        $this->annee_installation = $annee_installation;
        $this->controle_coherence();
        return $this;
    }

    public function set_ventilation_hybride(
        TypeVentilation $type_ventilation,
        TypeInstallation $type_installation,
        ?AnneeInstallation $annee_installation = null,
    ): self {
        if (false === $type_ventilation->ventilation_hybride()) {
            throw new \InvalidArgumentException("Le type de ventilation n'est pas hybride");
        }
        $this->type_ventilation = $type_ventilation;
        $this->type_installation = $type_installation;
        $this->annee_installation = $annee_installation;
        $this->controle_coherence();
        return $this;
    }

    public function controle_coherence(): void
    {
        if ($this->annee_installation && $this->annee_installation->valeur() < $this->batiment->annee_construction()->valeur()) {
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

    public function type_ventilation(): TypeVentilation
    {
        return $this->type_ventilation;
    }

    public function type_installation(): ?TypeInstallation
    {
        return $this->type_installation;
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return $this->annee_installation;
    }
}
