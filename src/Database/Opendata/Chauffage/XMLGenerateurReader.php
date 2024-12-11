<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLReader;
use App\Domain\Chauffage\Enum\{EnergieGenerateur, LabelGenerateur, PositionChaudiere, TypeCombustion, TypeDistribution, TypeGenerateur};
use App\Domain\Chauffage\ValueObject\{Combustion, Reseau, Signaletique};
use App\Domain\Common\Type\Id;

/**
 * Par défaut, les types de générateurs "PAC Hybride - partie ..." sont considérés comme des PAC hybrides air/eau
 */
final class XMLGenerateurReader extends XMLReader
{
    public function apply(): bool
    {
        return false === \in_array($this->enum_type_generateur_ch_id(), [145, 146, 147, 162, 163, 164, 165, 166, 167, 168, 169, 170]);
    }

    public function read_installation(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->xml()->findOneOrError('./ancestor::installation_chauffage'));
    }

    /** @return XMLEmetteurReader[] */
    public function read_emetteurs(): array
    {
        return \array_filter(
            $this->read_installation()->read_emetteurs(),
            fn(XMLEmetteurReader $item): bool => $item->apply() && $item->enum_lien_generateur_emetteur_id() === $this->enum_lien_generateur_emetteur_id(),
        );
    }

    public function match(string $reference): bool
    {
        $patterns = [
            $this->reference(),
            $this->generateur_mixte_reference(),
            \preg_replace('/(#\d+)/', '', $this->reference()),
            \preg_replace('/(#\d+)/', '', $this->generateur_mixte_reference()),
            $this->xml()->findOne('.//description')?->reference(),
        ];

        foreach ($patterns as $p) {
            if ($p === $reference) {
                return true;
            }
        }
        return false;
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
        if (null === $reference = $this->generateur_mixte_reference()) {
            return null;
        }
        foreach ($this->xml()->etat_initial()->read_ecs()->read_generateurs() as $item) {
            if ($item->match($reference)) {
                return $item->id();
            }
        }
        throw new \RuntimeException("Générateur mixte {$reference} non trouvé");
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
            type: $this->type_generateur(),
            energie: $this->energie_generateur(),
            type_partie_chaudiere: $this->type_partie_chaudiere(),
            energie_partie_chaudiere: $this->energie_partie_chaudiere(),
            position_chaudiere: $this->position_chaudiere(),
            label: $this->label(),
            priorite_cascade: $this->priorite_generateur_cascade(),
            pn: $this->pn_saisi(),
            scop: $this->scop_saisi(),
            combustion: $this->combustion(),
        );
    }

    public function combustion(): ?Combustion
    {
        return $this->type_combustion() ? new Combustion(
            type: $this->type_combustion(),
            presence_ventouse: $this->presence_ventouse(),
            presence_regulation_combustion: $this->presence_regulation_combustion(),
            pveilleuse: $this->pveilleuse_saisi(),
            qp0: $this->qp0_saisi(),
            rpn: $this->rpn_saisi(),
            rpint: $this->rpint_saisi(),
            tfonc30: $this->tfonc30_saisi(),
            tfonc100: $this->tfonc100_saisi(),
        ) : null;
    }

    public function type_combustion(): ?TypeCombustion
    {
        return TypeCombustion::from_enum_type_generateur_ch_id($this->enum_type_generateur_ch_id());
    }

    public function generateur_appoint(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 2;
    }

    public function is_appoint_electrique_sdb(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 3;
    }

    public function generateur_collectif(): bool
    {
        return $this->enum_lien_generateur_emetteur_id() === 1 && $this->read_installation()->installation_collective();
    }

    public function type_generateur(): TypeGenerateur
    {
        return TypeGenerateur::from_type_generateur_ch_id($this->enum_type_generateur_ch_id());
    }

    public function position_chaudiere(): ?PositionChaudiere
    {
        return $this->type_generateur()->is_chaudiere() ? match (true) {
            ($this->pn() < 18) => PositionChaudiere::CHAUDIERE_MURALE,
            ($this->pn() >= 18) => PositionChaudiere::CHAUDIERE_SOL,
            default =>  PositionChaudiere::CHAUDIERE_SOL,
        } : null;
    }

    public function type_partie_chaudiere(): ?TypeGenerateur
    {
        return match ($this->enum_type_generateur_ch_id()) {
            148, 149, 150, 151, 152, 153, 154, 155, 156, 157, 158, 159, 160, 161 => TypeGenerateur::CHAUDIERE,
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

    public function reseau(): ?Reseau
    {
        $installation = $this->read_installation();

        return ($type_distribution = $this->type_distribution()) && $this->type_generateur()->is_chauffage_central() ? new Reseau(
            type_distribution: $type_distribution,
            presence_circulateur_externe: $installation->presence_circulateur_externe(),
            niveaux_desservis: $installation->niveaux_desservis(),
            isolation_reseau: $installation->isolation_reseau(),
        ) : null;
    }

    public function type_distribution(): ?TypeDistribution
    {
        foreach ($this->read_emetteurs() as $emetteur_reader) {
            return $emetteur_reader->type_distribution();
        }
        return null;
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
