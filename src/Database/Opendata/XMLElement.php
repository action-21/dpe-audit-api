<?php

namespace App\Database\Opendata;

use App\Domain\Common\Type\Id;

class XMLElement extends \SimpleXMLElement
{
    public function audit(): static
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
        $value = preg_replace('!\s+!', ' ', $this->strval());
        $value = \str_replace(' ', '-', $value);
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
        return (float) $this;
    }

    public function intval(): int
    {
        return (int) $this;
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

    /** @return self[] */
    public function logement_visite_collection(): array
    {
        return $this->audit()->findMany('.//logement_visite_collection//logement_visite');
    }

    /** @return static[] */
    public function ets_collection(): array
    {
        return $this->audit()->findMany('.//ets_collection//ets');
    }

    /** @return static[] */
    public function mur_collection(): array
    {
        return $this->audit()->findMany('.//mur_collection//mur');
    }

    /** @return static[] */
    public function plancher_bas_collection(): array
    {
        return $this->audit()->findMany('.//plancher_bas_collection//plancher_bas');
    }

    /** @return static[] */
    public function plancher_haut_collection(): array
    {
        return $this->audit()->findMany('.//plancher_haut_collection//plancher_haut');
    }

    /** @return static[] */
    public function baie_collection(): array
    {
        return $this->audit()->findMany('.//baie_vitree_collection//baie_vitree');
    }

    /** @return static[] */
    public function porte_collection(): array
    {
        return $this->audit()->findMany('.//porte_collection//porte');
    }

    /** @return static[] */
    public function pont_thermique_collection(): array
    {
        return $this->audit()->findMany('.//pont_thermique_collection//pont_thermique');
    }

    /**
     * TODO: identifier les installations par appartement dans le cas d'un Audit-DPE immeuble
     * 
     * @return static[]
     */
    public function ventilation_collection(): array
    {
        return $this->audit()->findMany('.//ventilation_collection//ventilation');
    }

    /** @return static[] */
    public function climatisation_collection(): array
    {
        return $this->audit()->findMany('.//climatisation_collection//climatisation');
    }

    /** @return static[] */
    public function installation_chauffage_collection(): array
    {
        return $this->audit()->findMany('.//installation_chauffage_collection//installation_chauffage');
    }

    /** @return static[] */
    public function generateur_chauffage_collection(): array
    {
        return $this->audit()->findMany('.//generateur_chauffage_collection//generateur_chauffage');
    }

    /** @return static[] */
    public function emetteur_chauffage_collection(): array
    {
        return $this->audit()->findMany('.//emetteur_chauffage_collection//emetteur_chauffage');
    }

    /** @return static[] */
    public function installation_ecs_collection(): array
    {
        return $this->audit()->findMany('.//installation_ecs_collection//installation_ecs');
    }

    /** @return static[] */
    public function generateur_ecs_collection(): array
    {
        return $this->audit()->findMany('.//generateur_ecs_collection//generateur_ecs');
    }

    /** @return static[] */
    public function panneaux_pv_collection(): array
    {
        return $this->audit()->findMany('.//panneaux_pv_collection//panneaux_pv');
    }
}
