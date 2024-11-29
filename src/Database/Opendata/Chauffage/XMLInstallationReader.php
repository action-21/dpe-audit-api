<?php

namespace App\Database\Opendata\Chauffage;

use App\Database\Opendata\XMLReaderIterator;
use App\Domain\Chauffage\Enum\IsolationReseau;
use App\Domain\Chauffage\Enum\TypeDistribution;
use App\Domain\Chauffage\ValueObject\Regulation;
use App\Domain\Chauffage\ValueObject\Reseau;
use App\Domain\Chauffage\ValueObject\Solaire;
use App\Domain\Common\Type\Id;

final class XMLInstallationReader extends XMLReaderIterator
{
    private ?XMLGenerateurReader $generateur_collection = null;
    private ?XMLEmetteurReader $emetteur_collection = null;

    public function read_generateurs(): XMLGenerateurReader
    {
        if (null === $this->generateur_collection) {
            $this->generateur_collection = XMLGenerateurReader::from(
                $this->xml()->findMany('.//generateur_chauffage_collection/generateur_chauffage')
            );
        }
        return $this->generateur_collection;
    }

    public function read_emetteurs(): XMLEmetteurReader
    {
        if (null === $this->emetteur_collection) {
            $this->emetteur_collection = XMLEmetteurReader::from(
                $this->xml()->findMany('.//emetteur_chauffage_collection/emetteur_chauffage')
            );
        }
        return $this->emetteur_collection;
    }

    /**
     * Reconstitution des données pour les configuration avec générateur électrique dans la salle de bain
     */
    public function has_appoint_electrique_sdb(): bool
    {
        return \in_array($this->enum_cfg_installation_ch_id(), [4, 5]);
    }

    public function id(): Id
    {
        return $this->xml()->findOneOrError('.//reference')->id();
    }

    public function description(): string
    {
        return $this->xml()->findOne('.//description')?->strval() ?? 'Installation non décrite';
    }

    public function surface(): float
    {
        return $this->xml()->findOneOrError('.//surface_chauffee')->floatval();
    }

    public function regulation_centrale(): Regulation
    {
        return new Regulation(
            presence_regulation: $this->presence_regulation_centrale(),
            minimum_temperature: $this->regulation_centrale_minimum_temperature(),
            detection_presence: $this->regulation_centrale_detection_presence()
        );
    }

    public function regulation_terminale(): Regulation
    {
        return new Regulation(
            presence_regulation: $this->presence_regulation_terminale(),
            minimum_temperature: $this->regulation_terminale_minimum_temperature(),
            detection_presence: $this->regulation_terminale_detection_presence()
        );
    }

    public function isolation_reseau(): ?IsolationReseau
    {
        foreach ($this->read_emetteurs() as $item) {
            if (false === $item->apply())
                continue;
            if ($item->reseau_distribution_isole() !== null)
                return $item->reseau_distribution_isole() ? IsolationReseau::ISOLE : IsolationReseau::NON_ISOLE;

            return IsolationReseau::INCONNU;
        }
        return null;
    }

    public function comptage_individuel(): ?bool
    {
        foreach ($this->read_emetteurs() as $item) {
            if (false === $item->apply())
                continue;
            if ($item->comptage_individuel() !== null)
                return $item->comptage_individuel();
        }
        return null;
    }

    public function installation_collective(): bool
    {
        return $this->enum_type_installation_id() === 2;
    }

    public function solaire(): ?Solaire
    {
        return \in_array($this->enum_cfg_installation_ch_id(), [2, 7]) ? new Solaire(
            fch: $this->fch_saisi(),
        ) : null;
    }

    public function reseau(): ?Reseau
    {
        return $this->has_reseau() ? new Reseau(
            presence_circulateur_externe: $this->presence_circulateur_externe(),
            niveaux_desservis: $this->niveaux_desservis(),
            isolation_reseau: $this->isolation_reseau(),
        ) : null;
    }

    public function has_reseau(): bool
    {
        foreach ($this->read_emetteurs() as $emetteur) {
            if (false === $emetteur->apply())
                continue;

            return true;
        }
        return false;
    }

    public function type_distribution(int $enum_lien_generateur_emetteur_id): TypeDistribution
    {
        foreach ($this->read_emetteurs() as $emetteur_reader) {
            if ($emetteur_reader->enum_lien_generateur_emetteur_id() !== $enum_lien_generateur_emetteur_id)
                continue;

            return $emetteur_reader->type_distribution();
        }
        return TypeDistribution::SANS;
    }

    public function presence_circulateur_externe(): bool
    {
        return true === $this->installation_collective() && $this->xml()->findOne('//conso_auxiliaire_distribution_ch')?->floatval() > 0;
    }

    public function presence_regulation_centrale(): bool
    {
        foreach ($this->read_emetteurs() as $item) {
            if (true === $item->presence_regulation_centrale())
                return true;
        }
        return false;
    }

    public function regulation_centrale_minimum_temperature(): bool
    {
        foreach ($this->read_emetteurs() as $item) {
            if (true === $item->regulation_centrale_minimum_temperature())
                return true;
        }
        return false;
    }

    public function regulation_centrale_detection_presence(): bool
    {
        foreach ($this->read_emetteurs() as $item) {
            if (true === $item->regulation_centrale_detection_presence())
                return true;
        }
        return false;
    }

    public function presence_regulation_terminale(): bool
    {
        foreach ($this->read_emetteurs() as $item) {
            if (true === $item->presence_regulation_terminale())
                return true;
        }
        return false;
    }

    public function regulation_terminale_minimum_temperature(): bool
    {
        foreach ($this->read_emetteurs() as $item) {
            if (true === $item->regulation_terminale_minimum_temperature())
                return true;
        }
        return false;
    }

    public function regulation_terminale_detection_presence(): bool
    {
        foreach ($this->read_emetteurs() as $item) {
            if (true === $item->regulation_terminale_detection_presence())
                return true;
        }
        return false;
    }

    public function niveaux_desservis(): int
    {
        return $this->xml()->findOneOrError('.//nombre_niveau_installation_ch')->intval();
    }

    public function enum_cfg_installation_ch_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_cfg_installation_ch_id')->intval();
    }

    public function enum_type_installation_id(): int
    {
        return $this->xml()->findOneOrError('.//enum_type_installation_id')->intval();
    }

    public function fch_saisi(): ?float
    {
        return $this->xml()->findOne('.//fch_saisi')?->floatval();
    }

    // Données intermédiaires

}
