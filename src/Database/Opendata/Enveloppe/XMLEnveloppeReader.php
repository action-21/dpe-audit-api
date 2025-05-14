<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\{XMLElement, XMLReader};
use App\Database\Opendata\Enveloppe\Baie\XMLBaieReader;
use App\Database\Opendata\Enveloppe\Lnc\{XMLLncReader, XMLEtsReader, XMLLncVirtuelReader};
use App\Database\Opendata\Enveloppe\Mur\XMLMurReader;
use App\Database\Opendata\Enveloppe\Niveau\XMLNiveauReader;
use App\Database\Opendata\Enveloppe\PlancherBas\XMLPlancherBasReader;
use App\Database\Opendata\Enveloppe\PlancherHaut\XMLPlancherHautReader;
use App\Database\Opendata\Enveloppe\PontThermique\XMLPontThermiqueReader;
use App\Database\Opendata\Enveloppe\Porte\XMLPorteReader;
use App\Domain\Enveloppe\Enum\{Exposition, Inertie};

final class XMLEnveloppeReader extends XMLReader
{
    public static function from(XMLElement $xml): static
    {
        return parent::from(static::root($xml));
    }

    /**
     * Un niveau unique est reconstitué pour l'enveloppe
     * 
     * @return XMLNiveauReader[]
     */
    public function niveaux(): array
    {
        return [XMLNiveauReader::from($this->xml)];
    }

    /**
     * Les espaces tampons solarisés sont récupérées depuis l'XML
     * 
     * Les locaux non chauffés sont reconstitués pour chaque paroi de l'enveloppe sur la base des
     * propriétés surface_aue et surface_aiu
     * 
     * @return XMLLncReader[]
     */
    public function locaux_non_chauffes(): array
    {
        $locaux_non_chauffes = array_map(
            fn(XMLElement $xml): XMLEtsReader => XMLEtsReader::from($xml),
            $this->findMany('//ets_collection//ets')
        );
        foreach ($this->parois() as $reader) {
            if (false === $reader->local_non_chauffe_virtuel()) {
                continue;
            }
            if (array_find(
                $locaux_non_chauffes,
                fn(XMLLncReader $item): bool => $item->id()->compare($reader->local_non_chauffe_id())
            )) {
                continue;
            }
            $locaux_non_chauffes[] = XMLLncVirtuelReader::from($reader->xml());
        }
        return $locaux_non_chauffes;
    }

    /**
     * @return XMLParoiReader[]
     */
    public function parois(): array
    {
        return array_merge(
            $this->murs(),
            $this->planchers_bas(),
            $this->planchers_hauts(),
            $this->baies(),
            $this->portes()
        );
    }

    /** @return XMLMurReader[] */
    public function murs(): array
    {
        $readers = [];

        foreach ($this->findMany('.//mur_collection//mur') as $item) {
            $reader = XMLMurReader::from($item);

            if (false === $reader->supports()) {
                continue;
            }
            $readers[] = $reader;
        }
        return $readers;
    }

    /** @return XMLPlancherBasReader[] */
    public function planchers_bas(): array
    {
        $readers = [];

        foreach ($this->findMany('.//plancher_bas_collection//plancher_bas') as $item) {
            $reader = XMLPlancherBasReader::from($item);

            if (false === $reader->supports()) {
                continue;
            }
            $readers[] = $reader;
        }
        return $readers;
    }

    /** @return XMLPlancherHautReader[] */
    public function planchers_hauts(): array
    {
        $readers = [];

        foreach ($this->findMany('.//plancher_haut_collection//plancher_haut') as $item) {
            $reader = XMLPlancherHautReader::from($item);

            if (false === $reader->supports()) {
                continue;
            }
            $readers[] = $reader;
        }
        return $readers;
    }

    /** @return XMLBaieReader[] */
    public function baies(): array
    {
        $readers = [];

        foreach ($this->findMany('.//baie_vitree_collection//baie_vitree') as $item) {
            $reader = XMLBaieReader::from($item);

            if (false === $reader->supports()) {
                continue;
            }
            for ($i = 1; $i <= $reader->nb_baie(); $i++) {
                $readers[] = $reader;
            }
        }
        return $readers;
    }

    /** @return XMLPorteReader[] */
    public function portes(): array
    {
        $readers = [];

        foreach ($this->findMany('.//porte_collection//porte') as $item) {
            $reader = XMLPorteReader::from($item);

            if (false === $reader->supports()) {
                continue;
            }
            for ($i = 1; $i <= $reader->nb_porte(); $i++) {
                $readers[] = $reader;
            }
        }
        return $readers;
    }

    /** @return XMLPontThermiqueReader[] */
    public function ponts_thermiques(): array
    {
        return array_filter(array_map(
            fn(XMLElement $xml): XMLPontThermiqueReader => XMLPontThermiqueReader::from($xml),
            $this->findMany('.//pont_thermique_collection//pont_thermique')
        ), fn(XMLPontThermiqueReader $reader) => $reader->supports());
    }

    public function plusieurs_facade_exposee(): bool
    {
        return $this->findOneOrError('//plusieurs_facade_exposee')->getValue();
    }

    public function q4pa_conv(): ?float
    {
        return $this->findOne('//q4pa_conv_saisi')?->floatval();
    }

    public function exposition(): Exposition
    {
        return $this->plusieurs_facade_exposee() ? Exposition::EXPOSITION_MULTIPLE : Exposition::EXPOSITION_SIMPLE;
    }

    public function presence_brasseurs_air(): bool
    {
        return $this->findOne('//brasseur_air')?->boolval() ?? false;
    }

    public function inertie(): Inertie
    {
        return Inertie::from_enum_classe_inertie_id($this->enum_classe_inertie_id());
    }

    public function enum_classe_inertie_id(): int
    {
        return $this->findOneOrError('//enum_classe_inertie_id')->intval();
    }

    public function inertie_paroi_verticale_lourd(): bool
    {
        return $this->findOneOrError('//inertie_paroi_verticale_lourd')->boolval();
    }

    public function inertie_plancher_haut_lourd(): bool
    {
        return $this->findOneOrError('//inertie_plancher_haut_lourd')->boolval();
    }

    public function inertie_plancher_bas_lourd(): bool
    {
        return $this->findOneOrError('//inertie_plancher_bas_lourd')->boolval();
    }

    // Données intermédiaires

    public function hvent(): float
    {
        return $this->findOneOrError('//deperdition/hvent')->floatval();
    }

    public function hperm(): float
    {
        return $this->findOneOrError('//deperdition/hperm')->floatval();
    }

    public function dr(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_renouvellement_air')->floatval();
    }

    public function dp(): float
    {
        return array_sum([
            $this->dp_murs(),
            $this->dp_plancher_bas(),
            $this->dp_plancher_haut(),
            $this->dp_baie(),
            $this->dp_porte(),
        ]);
    }

    public function dp_murs(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_mur')->floatval();
    }

    public function dp_plancher_bas(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_plancher_bas')->floatval();
    }

    public function dp_plancher_haut(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_plancher_haut')->floatval();
    }

    public function dp_baie(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_baie_vitree')->floatval();
    }

    public function dp_porte(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_porte')->floatval();
    }

    public function pt(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_pont_thermique')->floatval();
    }

    public function gv(): float
    {
        return $this->findOneOrError('//deperdition/deperdition_enveloppe')->floatval();
    }

    public function surface_sud_equivalente(): float
    {
        return $this->findOneOrError('//apport_et_besoin/surface_sud_equivalente')->floatval();
    }

    public function apport_solaire_fr(): float
    {
        return $this->findOneOrError('//apport_et_besoin/apport_solaire_fr')->floatval();
    }

    public function apport_interne_fr(): float
    {
        return $this->findOneOrError('//apport_et_besoin/apport_interne_fr')->floatval();
    }

    public function apport_solaire_ch(): float
    {
        return $this->findOneOrError('//apport_et_besoin/apport_solaire_ch')->floatval();
    }

    public function apport_interne_ch(): float
    {
        return $this->findOneOrError('//apport_et_besoin/apport_interne_ch')->floatval();
    }

    public function fraction_apport_gratuit_ch(bool $scenario_depensier = false): float
    {
        return $scenario_depensier
            ? $this->findOneOrError('//apport_et_besoin/fraction_apport_gratuit_depensier_ch')->floatval()
            : $this->findOneOrError('//apport_et_besoin/fraction_apport_gratuit_ch')->floatval();
    }
}
