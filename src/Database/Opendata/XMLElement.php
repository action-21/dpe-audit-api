<?php

namespace App\Database\Opendata;

use App\Domain\Common\Type\Id;
use App\Database\Opendata\Audit\XMLAuditReader;
use App\Database\Opendata\Chauffage\XMLChauffageReader;
use App\Database\Opendata\Ecs\XMLEcsReader;
use App\Database\Opendata\Enveloppe\XMLEnveloppeReader;
use App\Database\Opendata\Production\XMLProductionReader;
use App\Database\Opendata\Refroidissement\XMLRefroidissementReader;
use App\Database\Opendata\Ventilation\XMLVentilationReader;
use App\Database\Opendata\Visite\XMLLogementReader;

class XMLElement extends \SimpleXMLElement
{
    public function etat_initial(): static
    {
        return $this->findOneOfOrError([
            '/audit/logement_collection//logement[.//enum_scenario_id="0"]',
            '/dpe/logement'
        ]);
    }

    public function findOne(string $xpath): ?static
    {
        $result = $this->xpath($xpath)[0] ?? null;
        return false === empty($result) ? $result : null;
    }

    public function findOneOrError(string $xpath): static
    {
        if (null === $result = $this->findOne($xpath)) {
            throw new \RuntimeException("XPath expression '{$xpath}' did not match any elements");
        }
        return $result;
    }

    public function findOneOf(array $xpaths): ?static
    {
        foreach ($xpaths as $xpath) {
            $result = $this->xpath($xpath);
            if ($result = $this->xpath($xpath)[0] ?? null) {
                return false === empty($result) ? $result : null;
            }
        }
        return null;
    }

    public function findOneOfOrError(array $xpaths): static
    {
        if (null === $result = $this->findOneOf($xpaths)) {
            $expressions = \implode(', ', $xpaths);
            throw new \RuntimeException("XPath expressions {$expressions} did not match any elements");
        }
        return $result;
    }

    /** @return static[] */
    public function search(string $xpath): array
    {
        return \array_filter($this->xpath($xpath), fn($result): bool => $result instanceof static);
    }

    /** @return static[] */
    public function findMany(string $xpath): array
    {
        return \array_filter($this->xpath($xpath), fn($result): bool => $result instanceof static);
    }

    /** @return static[] */
    public function findManyOrError(string $xpath): array
    {
        $collection = \array_filter($this->xpath($xpath), fn($result): bool => $result instanceof static);
        if (empty($collection)) {
            throw new \RuntimeException("XPath expression '{$xpath}' did not match any elements");
        }
        return $collection;
    }

    /** @return static[] */
    public function findManyOf(array $xpaths): array
    {
        $results = [];
        foreach ($xpaths as $xpath) {
            $results = [...$results, ...$this->findMany($xpath)];
        }
        return $results;
    }

    public function id(): Id
    {
        $value = \trim($this->strval());
        $value = \strtolower($value);
        $value = \str_replace('generateur:', '', $value);
        $value = \str_replace('emetteur:', '', $value);
        $value = \str_replace('ets:', '', $value);
        $value = \preg_replace('/\s/', '', $value);
        return Id::from($value);
    }

    public function reference(): string
    {
        $value = \trim($this->strval());
        $value = \strtolower($value);
        $value = \preg_replace('/(#\d+)/', '', $value);
        $value = \str_replace('generateur:', '', $value);
        $value = \str_replace('emetteur:', '', $value);
        $value = \str_replace('ets:', '', $value);
        $value = \preg_replace('/\s/', '', $value);
        return Id::from($value);
    }

    public function getValue(): string
    {
        return (string) $this;
    }

    public function strval(): string
    {
        return (string) $this;
    }

    public function floatval(): float
    {
        return (float) $this->getValue();
    }

    public function intval(): int
    {
        return (int) $this->getValue();
    }

    public function boolval(): bool
    {
        return (bool) $this->intval();
    }

    public function orientation(): ?float
    {
        return match ($this->intval()) {
            1 => 180,
            2 => 0,
            3 => 90,
            4 => 270,
            5 => null,
        };
    }

    public function inclinaison(): ?float
    {
        return match ($this->intval()) {
            1 => 15,
            2 => 50,
            3 => 90,
            4 => null,
        };
    }

    public function annee_isolation(): int
    {
        return match ($this->intval()) {
            1 => 1947,
            2 => 1974,
            3 => 1977,
            4 => 1982,
            5 => 1988,
            6 => 2000,
            7 => 2005,
            8 => 2012,
            9 => 2021,
            10 => $this->annee_etablissement(),
        };
    }

    public function annee_etablissement(): int
    {
        $date = $this->findOneOfOrError(['//date_etablissement_audit', '//date_etablissement_dpe'])->strval();
        return (int) (new \DateTimeImmutable($date))->format('Y');
    }

    public function read_audit(): XMLAuditReader
    {
        return XMLAuditReader::from($this->etat_initial());
    }

    public function read_enveloppe(): XMLEnveloppeReader
    {
        return XMLEnveloppeReader::from($this->etat_initial());
    }

    public function read_chauffage(): XMLChauffageReader
    {
        return XMLChauffageReader::from($this->etat_initial());
    }

    public function read_ecs(): XMLEcsReader
    {
        return XMLEcsReader::from($this->etat_initial());
    }

    public function read_refroidissement(): XMLRefroidissementReader
    {
        return XMLRefroidissementReader::from($this->etat_initial());
    }

    public function read_production(): XMLProductionReader
    {
        return XMLProductionReader::from($this->etat_initial());
    }

    public function read_logements_visites(): XMLLogementReader
    {
        return XMLLogementReader::from($this->etat_initial()->findMany('.//logement_visite_collection//logement_visite'));
    }

    /**
     * TODO: identifier les installations par appartement dans le cas d'un Audit-DPE immeuble
     */
    public function read_ventilations(): XMLVentilationReader
    {
        return XMLVentilationReader::from($this->etat_initial()->findMany('.//ventilation_collection//ventilation'));
    }
}
