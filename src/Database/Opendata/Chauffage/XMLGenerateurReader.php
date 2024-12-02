<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Chauffage\Enum\{CategorieGenerateur, EnergieGenerateur, LabelGenerateur, TypeChaudiere, TypeChauffage, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\Signaletique;
use App\Domain\Common\Type\Id;

/**
 * Par défaut, les types de générateurs "PAC Hybride - partie ..." sont considérés comme des PAC hybrides air/eau
 */
final class XMLGenerateurReader extends XMLReaderIterator
{
    public function apply(): bool
    {
        return false === \in_array($this->enum_type_generateur_ch_id(), [145, 146, 147, 162, 163, 164, 165, 166, 167, 168, 169, 170]);
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function reference(): string
    {
        return $this->xml()->findOneOrError('.//reference')->reference();
    }

    public function generateur_mixte_id(): ?Id
    {
        return $this->xml()->findOne('.//reference_generateur_mixte')?->id();
    }

    public function match_generateur_mixte(): ?Id
    {
        if (null === $id = $this->generateur_mixte_id()) {
            return null;
        }
        foreach ($this->xml()->etat_initial()->read_ecs()->read_generateurs() as $item) {
            if ($id->compare($item->id())) {
                return $id;
            }
            if ($item->generateur_mixte_id() && $id->compare($item->generateur_mixte_id())) {
                return $id;
            }
            if ($item->reference() === $this->generateur_mixte_reference()) {
                return $item->id();
            }
            if ($item->generateur_mixte_reference() === $this->generateur_mixte_reference()) {
                return Id::from($item->generateur_mixte_reference());
            }
        }
        throw new \RuntimeException("Générateur mixte {$id->value} non trouvé", 400);
    }

    public function generateur_mixte_reference(): ?string
    {
        return $this->xml()->findOne('.//reference_generateur_mixte')?->reference();
    }

    public function reseau_chaleur_id(): ?Id
    {
        return $this->xml()->findOne('.//identifiant_reseau_chaleur')?->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Générateur non décrit';
    }

    public function signaletique(): Signaletique
    {
        return new Signaletique(
            type_chaudiere: $this->type_chaudiere(),
            label: $this->label(),
            presence_regulation_combustion: $this->presence_regulation_combustion(),
            presence_ventouse: $this->presence_ventouse(),
            priorite_cascade: $this->priorite_generateur_cascade(),
            pn: $this->pn_saisi(),
            rpn: $this->rpn_saisi(),
            rpint: $this->rpint_saisi(),
            tfonc30: $this->tfonc30_saisi(),
            tfonc100: $this->tfonc100_saisi(),
            qp0: $this->qp0_saisi(),
            pveilleuse: $this->pveilleuse_saisi(),
            scop: $this->scop_saisi(),
        );
    }

    public function categorie(): CategorieGenerateur
    {
        return CategorieGenerateur::determine(
            type_generateur: $this->type_generateur(),
            energie_generateur: $this->energie_generateur(),
        );
    }

    public function generateur_appoint(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 2;
    }

    public function is_appoint_electrique_sdb(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 3;
    }

    public function type_chauffage(): TypeChauffage
    {
        return TypeChauffage::from_categorie($this->categorie());
    }

    public function type_generateur(): TypeGenerateur
    {
        return TypeGenerateur::from_type_generateur_ch_id($this->enum_type_generateur_ch_id());
    }

    public function type_chaudiere(): ?TypeChaudiere
    {
        return match ($this->categorie()) {
            CategorieGenerateur::CHAUDIERE_BOIS,
            CategorieGenerateur::CHAUDIERE_ELECTRIQUE,
            CategorieGenerateur::CHAUDIERE_STANDARD,
            CategorieGenerateur::CHAUDIERE_BASSE_TEMPERATURE,
            CategorieGenerateur::CHAUDIERE_CONDENSATION => match (true) {
                ($this->pn() < 18) => TypeChaudiere::CHAUDIERE_MURALE,
                ($this->pn() >= 18) => TypeChaudiere::CHAUDIERE_SOL,
                default => null,
            },
            default => null,
        };
    }

    public function type_partie_chaudiere(): ?TypeGenerateur
    {
        return match ($this->enum_type_generateur_ch_id()) {
            148, 149, 150, 151, 160, 161 => TypeGenerateur::CHAUDIERE_CONDENSATION,
            152, 153, 154, 155, 156, 157, 158, 159 => TypeGenerateur::CHAUDIERE_STANDARD,
            default => null,
        };
    }

    public function energie_generateur(): EnergieGenerateur
    {
        return match ($this->enum_type_generateur_ch_id()) {
            148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161 => EnergieGenerateur::ELECTRICITE,
            default => EnergieGenerateur::from_enum_type_energie_id($this->enum_type_energie_id()),
        };
    }

    public function energie_partie_chaudiere(): ?EnergieGenerateur
    {
        return match ($this->enum_type_generateur_ch_id()) {
            148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161 => EnergieGenerateur::from_enum_type_energie_id($this->enum_type_energie_id()),
            default => null,
        };
    }

    public function annee_installation(): ?int
    {
        return match ($this->enum_type_generateur_ch_id()) {
            75 => 1969,
            76 => 1975,
            55, 62, 69, 120 => 1977,
            77, 85, 127 => 1980,
            86, 94, 128, 136 => 1985,
            20, 21, 22, 23 => 1989,
            78, 87, 129 => 1990,
            56, 63, 70, 121 => 1994,
            88, 91, 95, 130, 133, 137 => 2000,
            57, 64, 71, 122 => 2003,
            24, 25, 26, 27 => 2004,
            50, 53 => 2005,
            32, 33, 34, 35 => 2006,
            1, 4, 8, 12, 16 => 2007,
            44, 48, 140 => 2011,
            58, 65, 72, 123 => 2012,
            2, 5, 9, 13, 17, 145, 162, 165, 168 => 2014,
            79, 81, 83, 89, 92, 96, 131, 134, 138, 148, 150, 160 => 2015,
            6, 10, 14, 18, 146, 163, 166, 169 => 2016,
            36, 37, 38, 39, 59, 66, 124, 154, 157 => 2017,
            45, 60, 67, 73, 125, 152, 155, 158 => 2019,
            3, 7, 11, 15, 19, 28, 29, 30, 31, 40, 41, 42, 43, 46, 49, 51, 52, 54, 61, 68, 74, 80, 82,
            84, 90, 93, 97, 126, 132, 135, 139, 141, 147, 149, 151, 153, 156, 159, 161, 164, 167, 170 => $this->xml()->annee_etablissement(),
            default => null,
        };
    }

    public function label(): ?LabelGenerateur
    {
        return LabelGenerateur::from_enum_type_generateur_ch_id($this->enum_type_generateur_ch_id());
    }

    public function pn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->pn(),
            default => null,
        };
    }

    public function rpn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->rpn(),
            default => null,
        };
    }

    public function rpint_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->rpint(),
            default => null,
        };
    }

    public function qp0_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->qp0(),
            default => null,
        };
    }

    public function tfonc30_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->temp_fonc_30(),
            default => null,
        };
    }

    public function tfonc100_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->temp_fonc_100(),
            default => null,
        };
    }

    public function pveilleuse_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->pveilleuse(),
            default => null,
        };
    }

    public function scop_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->scop(),
            default => null,
        };
    }

    public function enum_type_generateur_ch_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_generateur_ch_id')->intval();
    }

    public function enum_usage_generateur_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_usage_generateur_id')->intval();
    }

    public function enum_type_energie_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_energie_id')->intval();
    }

    public function enum_lien_generateur_emetteur_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_lien_generateur_emetteur_id')->intval();
    }

    public function enum_methode_saisie_carac_sys_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_methode_saisie_carac_sys_id')->intval();
    }

    public function surface_chauffee(): float
    {
        return $this->xml()->findOneOrError('.//surface_chauffee')->floatval();
    }

    public function position_volume_chauffe(): bool
    {
        return $this->xml()->findOneOrError('.//position_volume_chauffe')->boolval();
    }

    public function presence_ventouse(): ?bool
    {
        return $this->xml()->findOne('.//presence_ventouse')?->boolval();
    }

    public function presence_regulation_combustion(): ?bool
    {
        return $this->xml()->findOne('.//presence_regulation_combustion')?->boolval();
    }

    public function priorite_generateur_cascade(): ?bool
    {
        return $this->xml()->findOne('.//priorite_generateur_cascade')?->intval();
    }

    public function n_radiateurs_gaz(): ?int
    {
        return $this->xml()->findOne('.//n_radiateurs_gaz')?->intval();
    }

    // Données intermédiaires

    public function scop(): ?float
    {
        return $this->xml()->findOne('.//scop')?->floatval();
    }

    public function pn(): ?float
    {
        return $this->xml()->findOne('.//pn')?->floatval();
    }

    public function qp0(): ?float
    {
        return $this->xml()->findOne('.//qp0')?->floatval();
    }

    public function pveilleuse(): ?float
    {
        return $this->xml()->findOne('.//pveilleuse')?->floatval();
    }

    public function temp_fonc_30(): ?float
    {
        return $this->xml()->findOne('.//temp_fonc_30')?->floatval();
    }

    public function temp_fonc_100(): ?float
    {
        return $this->xml()->findOne('.//temp_fonc_100')?->floatval();
    }

    public function rpn(): ?float
    {
        $value = $this->xml()->findOne('.//rpn')?->floatval();
        $value = $value && $value <= 2 ? $value * 100 : $value;
        return $value;
    }

    public function rpint(): ?float
    {
        $value = $this->xml()->findOne('.//rpint')?->floatval();
        $value = $value && $value <= 2 ? $value * 100 : $value;
        return $value;
    }

    public function rendement_generation(): ?float
    {
        return $this->xml()->findOne('.//rendement_generation')?->floatval();
    }
}
