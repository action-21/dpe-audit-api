<?php

namespace App\Database\Opendata;

use App\Domain\Common\Type\Id;
use App\Database\Opendata\Audit\XMLAuditReader;
use App\Database\Opendata\Baie\XMLBaieReader;
use App\Database\Opendata\Chauffage\XMLInstallationReader as XMLInstallationChauffageReader;
use App\Database\Opendata\Ecs\XMLInstallationReader as XMLInstallationEcsReader;
use App\Database\Opendata\Enveloppe\XMLEnveloppeReader;
use App\Database\Opendata\Lnc\XMLLncReader;
use App\Database\Opendata\Mur\XMLMurReader;
use App\Database\Opendata\PlancherBas\XMLPlancherBasReader;
use App\Database\Opendata\PlancherHaut\XMLPlancherHautReader;
use App\Database\Opendata\PontThermique\XMLPontThermiqueReader;
use App\Database\Opendata\Porte\XMLPorteReader;
use App\Database\Opendata\Production\XMLPanneauPvReader;
use App\Database\Opendata\Refroidissement\XMLClimatisationReader;
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

    /** @return static[] */
    public function ets_collection(): array
    {
        return $this->etat_initial()->findMany('.//ets_collection//ets');
    }

    public function read_audit(): XMLAuditReader
    {
        return XMLAuditReader::from($this->etat_initial());
    }

    public function read_enveloppe(): XMLEnveloppeReader
    {
        return XMLEnveloppeReader::from($this->etat_initial());
    }

    public function read_logements_visites(): XMLLogementReader
    {
        return XMLLogementReader::from($this->etat_initial()->findMany('.//logement_visite_collection//logement_visite'));
    }

    public function read_murs(): XMLMurReader
    {
        return XMLMurReader::from($this->etat_initial()->findMany('.//mur_collection//mur'));
    }

    public function read_planchers_bas(): XMLPlancherBasReader
    {
        return XMLPlancherBasReader::from($this->etat_initial()->findMany('.//plancher_bas_collection//plancher_bas'));
    }

    public function read_planchers_hauts(): XMLPlancherHautReader
    {
        return XMLPlancherHautReader::from($this->etat_initial()->findMany('.//plancher_haut_collection//plancher_haut'));
    }

    public function read_baies(): XMLBaieReader
    {
        return XMLBaieReader::from($this->etat_initial()->findMany('.//baie_vitree_collection//baie_vitree'));
    }

    public function read_portes(): XMLPorteReader
    {
        return XMLPorteReader::from($this->etat_initial()->findMany('.//porte_collection//porte'));
    }

    public function read_ponts_thermiques(): XMLPontThermiqueReader
    {
        return XMLPontThermiqueReader::from($this->etat_initial()->findMany('.//pont_thermique_collection//pont_thermique'));
    }

    /**
     * TODO: identifier les installations par appartement dans le cas d'un Audit-DPE immeuble
     */
    public function read_ventilations(): XMLVentilationReader
    {
        return XMLVentilationReader::from($this->etat_initial()->findMany('.//ventilation_collection//ventilation'));
    }

    public function read_climatisations(): XMLClimatisationReader
    {
        return XMLClimatisationReader::from($this->etat_initial()->findMany('.//climatisation_collection//climatisation'));
    }

    public function read_installations_chauffage(): XMLInstallationChauffageReader
    {
        return XMLInstallationChauffageReader::from($this->etat_initial()->findMany('.//installation_chauffage_collection//installation_chauffage'));
    }

    public function read_installations_ecs(): XMLInstallationEcsReader
    {
        return XMLInstallationEcsReader::from($this->etat_initial()->findMany('.//installation_ecs_collection//installation_ecs'));
    }

    public function read_panneaux_pv(): XMLPanneauPvReader
    {
        return XMLPanneauPvReader::from($this->etat_initial()->findMany('.//panneaux_pv_collection//panneaux_pv'));
    }
}
