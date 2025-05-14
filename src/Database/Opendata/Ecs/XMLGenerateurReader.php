<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\XMLReader;
use App\Domain\Common\ValueObject\{Annee, Id, Pourcentage};
use App\Domain\Ecs\Enum\{EnergieGenerateur, LabelGenerateur, ModeCombustion, TypeChaudiere, TypeGenerateur, UsageEcs};
use App\Domain\Ecs\ValueObject\Generateur\{Combustion, Signaletique};

final class XMLGenerateurReader extends XMLReader
{
    public function installation(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->findOneOrError('./ancestor::installation_ecs'));
    }

    public function match(array $references): bool
    {
        return count(array_intersect($this->references(), $references)) > 0;
    }

    public function references(): array
    {
        return [
            $this->reference(),
            $this->reference_generateur_mixte(),
            \preg_replace('/(#\d+)/', '', $this->reference()),
            \preg_replace('/(#\d+)/', '', $this->reference_generateur_mixte()),
            $this->findOne('.//description')?->reference(),
        ];
    }

    public function installations(): XMLInstallationReader
    {
        return XMLInstallationReader::from($this->findOneOrError('./ancestor::installation_ecs'));
    }

    public function id(): Id
    {
        return Id::from($this->reference());
    }

    public function reference(): string
    {
        return $this->findOneOrError('.//reference')->reference();
    }

    public function generateur_mixte_id(): ?Id
    {
        if (null === $reference = $this->reference_generateur_mixte()) {
            return null;
        }
        foreach ($this->chauffage()->generateurs() as $item) {
            if ($item->match($this->references())) {
                return $item->id();
            }
        }
        throw new \RuntimeException("Générateur mixte {$reference} non trouvé");
    }

    public function reference_generateur_mixte(): ?string
    {
        return $this->findOne('.//reference_generateur_mixte')?->reference();
    }

    public function reseau_chaleur_id(): ?Id
    {
        return $this->findOne('.//identifiant_reseau_chaleur')?->id();
    }

    public function description(): string
    {
        return $this->findOne('.//description')?->strval() ?? 'Générateur non décrit';
    }

    public function usage(): UsageEcs
    {
        return $this->generateur_mixte_id() ? UsageEcs::CHAUFFAGE_ECS : UsageEcs::ECS;
    }

    public function mode_combustion(): ?ModeCombustion
    {
        return ModeCombustion::from_enum_type_generateur_ecs_id($this->enum_type_generateur_ecs_id());
    }

    public function combustion(): ?Combustion
    {
        return $this->mode_combustion() ? Combustion::create(
            mode_combustion: $this->mode_combustion(),
            presence_ventouse: $this->presence_ventouse(),
            pveilleuse: $this->pveilleuse(),
            qp0: $this->qp0(),
            rpn: $this->rpn(),
        ) : null;
    }

    public function signaletique(): Signaletique
    {
        return Signaletique::create(
            volume_stockage: $this->volume_stockage(),
            type_chaudiere: $this->type_chaudiere(),
            label: $this->label(),
            pn: $this->pn(),
            cop: $this->cop(),
            combustion: $this->combustion(),
        );
    }

    public function generateur_collectif(): bool
    {
        return $this->installation()->installation_collective();
    }

    public function generateur_multi_batiment(): bool
    {
        return match ($this->enum_type_generateur_ecs_id()) {
            74, 75, 76, 77, 134 => true,
            default => false,
        };
    }

    public function type_chaudiere(): ?TypeChaudiere
    {
        return match ($this->type()) {
            TypeGenerateur::CHAUDIERE => match (true) {
                ($this->pn() < 18) => TypeChaudiere::CHAUDIERE_MURALE,
                ($this->pn() >= 18) => TypeChaudiere::CHAUDIERE_SOL,
                default =>  TypeChaudiere::CHAUDIERE_SOL,
            },
            default => null,
        };
    }

    public function type(): TypeGenerateur
    {
        if (null === $value = TypeGenerateur::from_enum_type_generateur_ecs_id($this->findOneOrError('.//enum_type_generateur_ecs_id')->intval())) {
            throw new \DomainException("Valeur hors méthode", 400);
        }
        return $value;
    }

    public function energie(): EnergieGenerateur
    {
        return EnergieGenerateur::from_enum_type_energie_id(
            $this->findOneOrError('.//enum_type_energie_id')->intval()
        );
    }

    public function annee_installation(): ?Annee
    {
        return match ($this->enum_type_generateur_ecs_id()) {
            35 => Annee::from(1969),
            36 => Annee::from(1975),
            15, 22, 29, 85 => Annee::from(1977),
            63, 110 => Annee::from(1979),
            37, 45, 92, 46, 54, 93, 101 => Annee::from(1980),
            58, 64, 105, 111 => Annee::from(1989),
            38, 47, 94 => Annee::from(1990),
            16, 23, 30, 86 => Annee::from(1994),
            48, 51, 55, 59, 61, 65, 95, 98, 102, 106, 108, 112 => Annee::from(2000),
            17, 24, 31, 87 => Annee::from(2003),
            1, 4, 7, 10 => Annee::from(2009),
            13, 115 => Annee::from(2011),
            18, 25, 32, 88 => Annee::from(2012),
            2, 5, 8, 11 => Annee::from(2014),
            39, 41, 43, 49, 52, 56, 66, 96, 99, 103, 113 => Annee::from(2015),
            19, 26, 89 => Annee::from(2017),
            20, 27, 33, 90 => Annee::from(2019),
            3, 6, 9, 12, 14, 21, 28, 34, 40, 42, 44, 50, 53, 57, 60, 62, 67, 91, 97, 100, 104, 107, 109,
            114, 116 => $this->audit()->annee_etablissement(),
            default => null,
        };
    }

    public function label(): ?LabelGenerateur
    {
        return LabelGenerateur::from_enum_type_generateur_ecs_id(
            $this->enum_type_generateur_ecs_id()
        );
    }

    public function stockage_integre(): bool
    {
        return $this->enum_type_stockage_ecs_id() === 3;
    }

    public function stockage_independant(): bool
    {
        return $this->enum_type_stockage_ecs_id() === 2;
    }

    public function position_volume_chauffe(): bool
    {
        return $this->findOneOrError('.//position_volume_chauffe')->boolval();
    }

    public function position_volume_chauffe_stockage(): ?bool
    {
        return $this->findOne('.//position_volume_chauffe_stockage')?->boolval();
    }

    public function volume_stockage(): float
    {
        return $this->findOneOrError('.//volume_stockage')->floatval();
    }

    public function presence_ventouse(): ?bool
    {
        return $this->findOne('.//presence_ventouse')?->boolval();
    }

    public function pn_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->pn(),
            default => null,
        };
    }

    public function rpn_saisi(): ?Pourcentage
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->rpn(),
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

    public function pveilleuse_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->pveilleuse(),
            default => null,
        };
    }

    public function cop_saisi(): ?float
    {
        return match ($this->enum_methode_saisie_carac_sys_id()) {
            2, 3, 4, 5, 6 => $this->cop(),
            default => null,
        };
    }

    public function enum_type_generateur_ecs_id(): int
    {
        return $this->findOneOrError('.//enum_type_generateur_ecs_id')->intval();
    }

    public function enum_usage_generateur_id(): int
    {
        return $this->findOneOrError('.//enum_usage_generateur_id')->intval();
    }

    public function enum_type_energie_id(): int
    {
        return $this->findOneOrError('.//enum_type_energie_id')->intval();
    }

    public function enum_methode_saisie_carac_sys_id(): int
    {
        return $this->findOneOrError('.//enum_methode_saisie_carac_sys_id')->intval();
    }

    public function enum_periode_installation_ecs_thermo_id(): ?int
    {
        return $this->findOne('.//enum_periode_installation_ecs_thermo_id')?->intval();
    }

    public function enum_type_stockage_ecs_id(): int
    {
        return $this->findOneOrError('.//enum_type_stockage_ecs_id')->intval();
    }

    // Données intermédiaires

    public function cop(): ?float
    {
        return $this->findOne('.//cop')?->floatval();
    }

    public function pn(): ?float
    {
        return ($value = $this->findOne('.//pn')?->floatval()) ? $value / 1000 : null;
    }

    public function qp0(): ?float
    {
        return $this->findOne('.//qp0')?->floatval();
    }

    public function pveilleuse(): ?float
    {
        return $this->findOne('.//pveilleuse')?->floatval();
    }

    public function rpn(): ?Pourcentage
    {
        if (null === $value = $this->findOne('.//rpn')?->floatval()) {
            return null;
        }
        return $value <= 2 ? Pourcentage::from($value * 100) : Pourcentage::from($value);
    }

    public function rpint(): ?Pourcentage
    {
        if (null === $value = $this->findOne('.//rpint')?->floatval()) {
            return null;
        }
        return $value <= 2 ? Pourcentage::from($value * 100) : Pourcentage::from($value);
    }

    public function rendement_generation(): ?float
    {
        return $this->findOne('.//rendement_generation')?->floatval();
    }

    public function rendement_generation_stockage(): ?float
    {
        return $this->findOne('.//rendement_generation_stockage')?->floatval();
    }

    public function rendement_stockage(): ?float
    {
        return $this->findOne('.//rendement_stockage')?->floatval();
    }
}
