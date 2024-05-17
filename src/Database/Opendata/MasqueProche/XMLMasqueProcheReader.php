<?php

namespace App\Database\Opendata\MasqueProche;

use App\Database\Opendata\{XMLElement, XMLReaderIterator};
use App\Domain\Common\Enum\Orientation;
use App\Domain\Common\Identifier\{Reference, Uuid};
use App\Domain\MasqueProche\Enum\TypeMasqueProche;
use App\Domain\MasqueProche\Table\{Fe1, Fe1Repository};
use App\Domain\MasqueProche\ValueObject\{Avancee, OrientationMasque};

final class XMLMasqueProcheReader extends XMLReaderIterator
{
    public function __construct(private Fe1Repository $table_fe1_repository)
    {
    }

    public function id(): \Stringable
    {
        return Uuid::create();
    }

    public function reference_baie(): \Stringable
    {
        return Reference::create($this->get()->findOneOrError('.//reference')->getValue());
    }

    public function description(): string
    {
        return 'Masque proche non décrit';
    }

    public function tv_coef_masque_proche_id(): int
    {
        return (int) $this->get()->findOneOrError('.//tv_coef_masque_proche_id')->getValue();
    }

    // Données déduites

    public function table_fe1(): Fe1
    {
        $collection = $this->table_fe1_repository->search_by(tv_coef_masque_proche_id: $this->tv_coef_masque_proche_id());
        if ($collection->count() === 0) {
            throw new \RuntimeException("Aucune donnée trouvée pour l'id . {$this->tv_coef_masque_proche_id()}");
        }
        return $collection->first();
    }

    public function enum_type_masque_proche(): TypeMasqueProche
    {
        return $this->table_fe1()->type_masque_proche;
    }

    public function avancee(): ?Avancee
    {
        return ($value = $this->table_fe1()->avancee_defaut) ? Avancee::from($value) : null;
    }

    public function enum_orientation(): ?Orientation
    {
        return $this->table_fe1()->orientation;
    }

    public function orientation(): ?OrientationMasque
    {
        return ($value = $this->enum_orientation()) ? OrientationMasque::from($value->to_azimut()) : null;
    }

    public static function apply(XMLElement $xml): bool
    {
        $tv_coef_masque_proche_id = (int) $xml->findOneOrError('.//tv_coef_masque_proche_id')->getValue();
        return $tv_coef_masque_proche_id !== 19;
    }

    public function read(XMLElement $xml): self
    {
        $xml = $xml->findOneOfOrError(['/audit/logement_collection//logement[.//enum_scenario_id="0"]', '/dpe/logement']);
        $this->array = \array_filter($xml->findMany('.//baie_vitree_collection//baie_vitree'), fn (XMLElement $element) => self::apply($element));
        return $this;
    }
}
