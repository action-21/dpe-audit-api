<?php

namespace App\Database\Opendata\Lnc;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Identifier\Uuid;
use App\Domain\Lnc\Enum\TypeLnc;
use App\Domain\Paroi\Enum\{IsolationLnc, Mitoyennete};

final class XMLLncReader extends XMLReaderIterator
{
    public function __construct(private XMLLncParoiReader $paroi_reader)
    {
    }

    // Données déduites

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function description(): string
    {
        return 'Local non chauffé non décrit';
    }

    public function enum_type_adjacence_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_type_adjacence_id')->getValue();
    }

    public function enum_cfg_isolation_lnc_id(): int
    {
        return (int) $this->get()->findOneOrError('.//enum_cfg_isolation_lnc_id')->getValue();
    }

    public function enum_isolation_lnc(): IsolationLnc
    {
        return IsolationLnc::from($this->enum_cfg_isolation_lnc_id());
    }

    public function enum_mitoyennete(): Mitoyennete
    {
        return Mitoyennete::from_type_adjacence_id($this->enum_type_adjacence_id());
    }

    public function type_lnc(): TypeLnc
    {
        if (null === $type_lnc = TypeLnc::try_from_type_adjacence_id($this->enum_type_adjacence_id())) {
            throw new \Exception("Type de local non chauffé introuvable pour le type d'adjacence {$this->enum_type_adjacence_id()}");
        }
        return $type_lnc;
    }

    public function surface_aue(): float
    {
        return (float) $this->get()->findOneOrError('.//surface_aue')->getValue();
    }

    public function paroi_reader(): XMLLncParoiReader
    {
        return $this->paroi_reader->read($this);
    }

    public static function apply(XMLElement $xml): bool
    {
        $enum_type_adjacence_id = (int) $xml->findOneOrError('.//enum_type_adjacence_id')->getValue();
        return Mitoyennete::from_type_adjacence_id($enum_type_adjacence_id) === Mitoyennete::LOCAL_NON_CHAUFFE;
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = \array_filter($xml->findManyOf([
            './/mur_collection/mur',
            './/plancher_bas_collection/plancher_bas',
            './/plancher_haut_collection/plancher_haut',
            './/baie_vitree_collection/baie_vitree',
            './/porte_collection/porte',
        ]), fn (XMLElement $item): bool => self::apply($item));
        return $this;
    }
}
