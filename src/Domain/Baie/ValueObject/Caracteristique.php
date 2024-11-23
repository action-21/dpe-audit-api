<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Baie;
use App\Domain\Baie\Enum\{TypeBaie, TypeFermeture};
use App\Domain\Baie\Enum\TypeBaie\{Fenetre, ParoiVitree, PorteFenetre};
use App\Domain\Common\Service\Assert;

final class Caracteristique
{
    public function __construct(
        public readonly TypeBaie $type,
        public readonly float $surface,
        public readonly float $inclinaison,
        public readonly TypeFermeture $type_fermeture,
        public readonly bool $presence_protection_solaire,
        public readonly ?int $annee_installation,
        public readonly ?bool $presence_soubassement = null,
        public readonly ?Menuiserie $menuiserie = null,
        public readonly ?float $ug = null,
        public readonly ?float $uw = null,
        public readonly ?float $ujn = null,
        public readonly ?float $sw = null,
    ) {}

    public static function create_paroi_vitree(
        ParoiVitree $type,
        float $surface,
        int $inclinaison,
        TypeFermeture $type_fermeture,
        bool $presence_protection_solaire,
        ?int $annee_installation,
        ?float $ug,
        ?float $uw,
        ?float $ujn,
        ?float $sw,
    ): self {
        return new self(
            type: $type->type_baie(),
            surface: $surface,
            inclinaison: $inclinaison,
            type_fermeture: $type_fermeture,
            presence_protection_solaire: $presence_protection_solaire,
            annee_installation: $annee_installation,
            ug: $ug,
            uw: $uw,
            ujn: $ujn,
            sw: $sw,
        );
    }

    public static function create_fenetre(
        Fenetre $type,
        float $surface,
        int $inclinaison,
        TypeFermeture $type_fermeture,
        bool $presence_protection_solaire,
        Menuiserie $menuiserie,
        ?int $annee_installation,
        ?float $ug,
        ?float $uw,
        ?float $ujn,
        ?float $sw,
    ): self {
        return new self(
            type: $type->type_baie(),
            surface: $surface,
            inclinaison: $inclinaison,
            type_fermeture: $type_fermeture,
            presence_protection_solaire: $presence_protection_solaire,
            menuiserie: $menuiserie,
            annee_installation: $annee_installation,
            ug: $ug,
            uw: $uw,
            ujn: $ujn,
            sw: $sw,
        );
    }

    public static function create_porte_fenetre(
        PorteFenetre $type,
        float $surface,
        int $inclinaison,
        TypeFermeture $type_fermeture,
        bool $presence_soubassement,
        bool $presence_protection_solaire,
        Menuiserie $menuiserie,
        ?int $annee_installation,
        ?float $ug,
        ?float $uw,
        ?float $ujn,
        ?float $sw,
    ): self {
        return new self(
            type: $type->type_baie(),
            surface: $surface,
            inclinaison: $inclinaison,
            type_fermeture: $type_fermeture,
            presence_soubassement: $presence_soubassement,
            presence_protection_solaire: $presence_protection_solaire,
            menuiserie: $menuiserie,
            annee_installation: $annee_installation,
            ug: $ug,
            uw: $uw,
            ujn: $ujn,
            sw: $sw,
        );
    }

    public function controle(Baie $entity): void
    {
        Assert::positif($this->surface);
        Assert::inclinaison($this->inclinaison);
        Assert::positif($this->ug);
        Assert::positif($this->uw);
        Assert::positif($this->ujn);
        Assert::positif($this->sw);
        Assert::annee($this->annee_installation);
        Assert::superieur_ou_egal_a($this->annee_installation, $entity->enveloppe()->annee_construction_batiment());
        $this->menuiserie?->controle();
    }
}
