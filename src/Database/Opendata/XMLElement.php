<?php

namespace App\Database\Opendata;

class XMLElement extends \SimpleXMLElement
{
    public function findOne(string $xpath): ?static
    {
        $result = $this->xpath($xpath);
        return $result ? $result[0] : null;
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
            if ($result = $this->xpath($xpath)) {
                return $result[0];
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

    /**
     * @return static[]
     */
    public function search(string $xpath): array
    {
        return \array_filter($this->xpath($xpath), fn ($result): bool => $result instanceof static);
    }

    /**
     * @return static[]
     */
    public function findMany(string $xpath): array
    {
        return \array_filter($this->xpath($xpath), fn ($result): bool => $result instanceof static);
    }

    /**
     * @return static[]
     */
    public function findManyOrError(string $xpath): array
    {
        $collection = \array_filter($this->xpath($xpath), fn ($result): bool => $result instanceof static);
        if (empty($collection)) {
            throw new \RuntimeException("XPath expression '{$xpath}' did not match any elements");
        }
        return $collection;
    }

    /**
     * @return static[]
     */
    public function findManyOf(array $xpaths): array
    {
        $results = [];
        foreach ($xpaths as $xpath) {
            $results = [...$results, ...$this->findMany($xpath)];
        }
        return $results;
    }

    public function getValue(): string
    {
        return (string) $this;
    }
}
