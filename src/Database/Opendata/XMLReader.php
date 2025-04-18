<?php

namespace App\Database\Opendata;

use App\Database\Opendata\Audit\XMLAuditReader;
use App\Database\Opendata\Chauffage\XMLChauffageReader;
use App\Database\Opendata\Ecs\XMLEcsReader;
use App\Database\Opendata\Enveloppe\XMLEnveloppeReader;
use App\Database\Opendata\Production\XMLProductionReader;
use App\Database\Opendata\Refroidissement\XMLRefroidissementReader;
use App\Database\Opendata\Ventilation\XMLVentilationReader;

abstract class XMLReader
{
    private array $cache = [];

    public function __construct(protected readonly XMLElement $xml) {}

    public static function from(XMLElement $xml): static
    {
        return new static($xml);
    }

    public function xml(): XMLElement
    {
        return $this->xml;
    }

    /**
     * @return XMLElement[]
     */
    public function get(string $xpath): array
    {
        return $this->cache[$xpath] ?? $this->cache[$xpath] = $this->xml->xpath($xpath);
    }

    public static function root(XMLElement $xml): XMLElement
    {
        return (new static($xml))->findOneOfOrError([
            '/audit/logement_collection//logement[.//enum_scenario_id="0"]',
            '/dpe/logement'
        ]);
    }

    public function audit(): XMLAuditReader
    {
        return XMLAuditReader::from($this->xml());
    }

    public function enveloppe(): XMLEnveloppeReader
    {
        return XMLEnveloppeReader::from($this->xml());
    }

    public function ventilation(): XMLVentilationReader
    {
        return XMLVentilationReader::from($this->xml());
    }

    public function chauffage(): XMLChauffageReader
    {
        return XMLChauffageReader::from($this->xml());
    }

    public function ecs(): XMLEcsReader
    {
        return XMLEcsReader::from($this->xml());
    }

    public function refroidissement(): XMLRefroidissementReader
    {
        return XMLRefroidissementReader::from($this->xml());
    }

    public function production(): XMLProductionReader
    {
        return XMLProductionReader::from($this->xml());
    }

    public function findOne(string $xpath): ?XMLElement
    {
        $result = $this->get($xpath)[0] ?? null;
        return false === empty($result) ? $result : null;
    }

    public function findOneOrError(string $xpath): XMLElement
    {
        if (null === $result = $this->findOne($xpath)) {
            throw new \RuntimeException("XPath expression '{$xpath}' did not match any elements");
        }
        return $result;
    }

    /**
     * @param string[] $xpaths
     */
    public function findOneOf(array $xpaths): ?XMLElement
    {
        foreach ($xpaths as $xpath) {
            if ($result = $this->get($xpath)[0] ?? null) {
                return false === empty($result) ? $result : null;
            }
        }
        return null;
    }

    /**
     * @param string[] $xpaths
     */
    public function findOneOfOrError(array $xpaths): XMLElement
    {
        if (null === $result = $this->findOneOf($xpaths)) {
            $expressions = \implode(', ', $xpaths);
            throw new \RuntimeException("XPath expressions {$expressions} did not match any elements");
        }
        return $result;
    }

    /**
     * @return XMLElement[]
     */
    public function findMany(string $xpath): array
    {
        return $this->get($xpath);
    }

    /**
     * @return XMLElement[]
     */
    public function findManyOrError(string $xpath): array
    {
        $result = $this->findMany($xpath);

        if (empty($result)) {
            throw new \RuntimeException("XPath expression '{$xpath}' did not match any elements");
        }
        return $result;
    }

    /**
     * @param string[] $xpaths
     * @return XMLElement[]
     */
    public function findManyOf(array $xpaths): array
    {
        $results = [];

        foreach ($xpaths as $xpath) {
            $results = [...$results, ...$this->findMany($xpath)];
        }
        return $results;
    }
}
