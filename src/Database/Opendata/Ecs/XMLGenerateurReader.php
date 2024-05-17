<?php

namespace App\Database\Opendata\Ecs;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Reference;
use App\Domain\Ecs\Enum\{EnergieGenerateur, TypeGenerateur, TypeStockage, UsageGenerateur};
use App\Domain\Ecs\ValueObject\{AnneeInstallation, Performance, Stockage, VolumeStockage};

final class XMLGenerateurReader extends XMLReaderIterator
{
    private XMLInstallationEcsReader $context;

    // * Données d'entrée

    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->get()->findOneOrError('.//reference');
    }

    public function description(): string
    {
        return $this->get()->findOne('.//description')?->getValue() ?? 'Générateur non décrit';
    }

    public function reference_generateur_mixte(): ?string
    {
        return $this->get()->findOne('.//reference_generateur_mixte')?->getValue();
    }

    public function identifiant_reseau_chaleur(): ?\Stringable
    {
        return ($value = $this->get()->findOne('.//identifiant_reseau_chaleur')) ? Reference::create($value->getValue()) : null;
    }

    public function enum_type_generateur_ecs_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_generateur_ecs_id');
    }

    public function type_generateur(): TypeGenerateur
    {
        return TypeGenerateur::from_enum_type_generateur_ecs_id($this->enum_type_generateur_ecs_id());
    }

    public function enum_usage_generateur_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_usage_generateur_id');
    }

    public function usage(): UsageGenerateur
    {
        return UsageGenerateur::from_enum_usage_generateur_id($this->enum_usage_generateur_id());
    }

    public function enum_type_energie_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_energie_id');
    }

    public function energie(): EnergieGenerateur
    {
        return EnergieGenerateur::from_enum_type_energie_id($this->enum_type_energie_id());
    }

    public function enum_periode_installation_ecs_thermo_id(): ?int
    {
        return ($value = $this->get()->findOne('.//enum_periode_installation_ecs_thermo_id')) ? (int) $value : null;
    }

    public function annee_installation(): ?AnneeInstallation
    {
        return ($value = $this->get()->findOne('.//annee_installation'))
            ? AnneeInstallation::from_enum_periode_installation_ecs_thermo_id((int) $value)
            : null;
    }

    public function enum_type_stockage_ecs_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_stockage_ecs_id');
    }

    public function type_stockage(): TypeStockage
    {
        return TypeStockage::from_enum_type_stockage_ecs_id($this->enum_type_stockage_ecs_id());
    }

    public function position_volume_chauffe(): bool
    {
        return (bool)(int) $this->get()->findOneOrError('.//position_volume_chauffe');
    }

    public function position_volume_chauffe_stockage(): ?bool
    {
        return ($value = $this->get()->findOne('.//position_volume_chauffe_stockage')) ? (bool)(int) $value : null;
    }

    public function volume_stockage(): ?VolumeStockage
    {
        return (0 < $value = (float) $this->get()->findOneOrError('.//volume_stockage')) ? VolumeStockage::from($value) : null;
    }

    public function presence_ventouse(): ?bool
    {
        return ($value = $this->get()->findOne('.//presence_ventouse')) ? (bool)(int) $value : null;
    }

    // * Données déduites

    public function stockage(): Stockage
    {
        return new Stockage(
            type_stockage: $this->type_stockage(),
            position_volume_chauffe: $this->position_volume_chauffe(),
            volume_stockage: $this->volume_stockage(),
        );
    }

    public function performance(): Performance
    {
        return new Performance(
            presence_ventouse: $this->presence_ventouse(),
            pn: null,
            rpn: null,
            qp0: null,
            pveilleuse: null,
            cop: null,
        );
    }

    // * Données intermédiaires

    public function pn(): ?float
    {
        return ($value = $this->get()->findOne('.//pn')) ? (float) $value : null;
    }

    public function qp0(): ?float
    {
        return ($value = $this->get()->findOne('.//qp0')) ? (float) $value : null;
    }

    public function pveilleuse(): ?float
    {
        return ($value = $this->get()->findOne('.//pveilleuse')) ? (float) $value : null;
    }

    public function rpn(): ?float
    {
        return ($value = $this->get()->findOne('.//rpn')) ? (float) $value : null;
    }

    public function cop(): ?float
    {
        return ($value = $this->get()->findOne('.//cop')) ? (float) $value : null;
    }

    public function ratio_besoin_ecs(): float
    {
        return (float) $this->get()->findOneOrError('.//ratio_besoin_ecs');
    }

    public function rendement_generation(): ?float
    {
        return ($value = $this->get()->findOne('.//rendement_generation')) ? (float) $value : null;
    }

    public function rendement_generation_stockage(): ?float
    {
        return ($value = $this->get()->findOne('.//rendement_generation_stockage')) ? (float) $value : null;
    }

    public function rendement_stockage(): ?float
    {
        return ($value = $this->get()->findOne('.//rendement_stockage')) ? (float) $value : null;
    }

    public function conso_ecs(): float
    {
        return (float) $this->get()->findOneOrError('.//conso_ecs');
    }

    public function conso_ecs_depensier(): float
    {
        return (float) $this->get()->findOneOrError('.//conso_ecs_depensier');
    }

    public function read(XMLElement $xml, XMLInstallationEcsReader $context): self
    {
        $this->context = $context;
        $this->array = $xml->findManyOrError('.//generateur_ecs_collection//generateur_ecs');
        return $this;
    }
}
