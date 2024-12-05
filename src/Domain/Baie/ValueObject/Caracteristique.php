<?php

namespace App\Domain\Baie\ValueObject;

use App\Domain\Baie\Enum\{TypeBaie, TypeFermeture};
use Webmozart\Assert\Assert;

final class Caracteristique
{
    public function __construct(
        public readonly TypeBaie $type,
        public readonly float $surface,
        public readonly float $inclinaison,
        public readonly TypeFermeture $type_fermeture,
        public readonly ?bool $presence_protection_solaire,
        public readonly ?int $annee_installation,
        public readonly ?bool $presence_soubassement = null,
        public readonly ?Menuiserie $menuiserie = null,
        public readonly ?Vitrage $vitrage = null,
        public readonly ?float $ug = null,
        public readonly ?float $uw = null,
        public readonly ?float $ujn = null,
        public readonly ?float $sw = null,
    ) {}

    public static function create_paroi_vitree(
        TypeBaie\ParoiVitree $type,
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
        Assert::greaterThan($surface, 0);
        Assert::greaterThanEq($inclinaison, 0);
        Assert::lessThanEq($inclinaison, 90);
        Assert::lessThanEq($annee_installation, (int) date('Y'));
        Assert::greaterThan($ug, 0);
        Assert::greaterThan($uw, 0);
        Assert::greaterThan($ujn, 0);
        Assert::greaterThan($sw, 0);

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
        TypeBaie\Fenetre $type,
        float $surface,
        int $inclinaison,
        TypeFermeture $type_fermeture,
        bool $presence_protection_solaire,
        Menuiserie $menuiserie,
        Vitrage $vitrage,
        ?int $annee_installation,
        ?float $ug,
        ?float $uw,
        ?float $ujn,
        ?float $sw,
    ): self {
        Assert::greaterThan($surface, 0);
        Assert::greaterThanEq($inclinaison, 0);
        Assert::lessThanEq($inclinaison, 90);
        Assert::lessThanEq($annee_installation, (int) date('Y'));
        Assert::greaterThan($ug, 0);
        Assert::greaterThan($uw, 0);
        Assert::greaterThan($ujn, 0);
        Assert::greaterThan($sw, 0);

        return new self(
            type: $type->type_baie(),
            surface: $surface,
            inclinaison: $inclinaison,
            type_fermeture: $type_fermeture,
            presence_protection_solaire: $presence_protection_solaire,
            menuiserie: $menuiserie,
            vitrage: $vitrage,
            annee_installation: $annee_installation,
            ug: $ug,
            uw: $uw,
            ujn: $ujn,
            sw: $sw,
        );
    }

    public static function create_porte_fenetre(
        TypeBaie\PorteFenetre $type,
        float $surface,
        int $inclinaison,
        TypeFermeture $type_fermeture,
        bool $presence_soubassement,
        bool $presence_protection_solaire,
        Menuiserie $menuiserie,
        Vitrage $vitrage,
        ?int $annee_installation,
        ?float $ug,
        ?float $uw,
        ?float $ujn,
        ?float $sw,
    ): self {
        Assert::greaterThan($surface, 0);
        Assert::greaterThanEq($inclinaison, 0);
        Assert::lessThanEq($inclinaison, 90);
        Assert::lessThanEq($annee_installation, (int) date('Y'));
        Assert::greaterThan($ug, 0);
        Assert::greaterThan($uw, 0);
        Assert::greaterThan($ujn, 0);
        Assert::greaterThan($sw, 0);

        return new self(
            type: $type->type_baie(),
            surface: $surface,
            inclinaison: $inclinaison,
            type_fermeture: $type_fermeture,
            presence_soubassement: $presence_soubassement,
            presence_protection_solaire: $presence_protection_solaire,
            menuiserie: $menuiserie,
            vitrage: $vitrage,
            annee_installation: $annee_installation,
            ug: $ug,
            uw: $uw,
            ujn: $ujn,
            sw: $sw,
        );
    }
}
