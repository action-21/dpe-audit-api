<?php

namespace App\Database\Opendata\Enveloppe;

use App\Database\Opendata\XMLReader;
use App\Domain\Enveloppe\Enum\Exposition;

final class XMLEnveloppeReader extends XMLReader
{
    public function plusieurs_facade_exposee(): bool
    {
        return $this->xml()->findOneOrError('//plusieurs_facade_exposee')->getValue();
    }

    public function q4pa_conv(): ?float
    {
        return $this->xml()->findOne('//q4pa_conv_saisi')?->floatval();
    }

    public function exposition(): Exposition
    {
        return $this->plusieurs_facade_exposee() ? Exposition::EXPOSITION_MULTIPLE : Exposition::EXPOSITION_SIMPLE;
    }

    // Données intermédiaires

    public function hvent(): float
    {
        return $this->xml()->findOneOrError('//deperdition/hvent')->floatval();
    }

    public function hperm(): float
    {
        return $this->xml()->findOneOrError('//deperdition/hperm')->floatval();
    }

    public function deperdition_renouvellement_air(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_renouvellement_air')->floatval();
    }

    public function deperdition_mur(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_mur')->floatval();
    }

    public function deperdition_plancher_bas(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_plancher_bas')->floatval();
    }

    public function deperdition_plancher_haut(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_plancher_haut')->floatval();
    }

    public function deperdition_baie(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_baie_vitree')->floatval();
    }

    public function deperdition_porte(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_porte')->floatval();
    }

    public function deperdition_pont_thermique(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_pont_thermique')->floatval();
    }

    public function deperdition_enveloppe(): float
    {
        return $this->xml()->findOneOrError('//deperdition/deperdition_enveloppe')->floatval();
    }

    public function surface_sud_equivalente(): float
    {
        return $this->xml()->findOneOrError('//apport_et_besoin/surface_sud_equivalente')->floatval();
    }

    public function apport_solaire_fr(): float
    {
        return $this->xml()->findOneOrError('//apport_et_besoin/apport_solaire_fr')->floatval();
    }

    public function apport_interne_fr(): float
    {
        return $this->xml()->findOneOrError('//apport_et_besoin/apport_interne_fr')->floatval();
    }

    public function apport_solaire_ch(): float
    {
        return $this->xml()->findOneOrError('//apport_et_besoin/apport_solaire_ch')->floatval();
    }

    public function apport_interne_ch(): float
    {
        return $this->xml()->findOneOrError('//apport_et_besoin/apport_interne_ch')->floatval();
    }

    public function fraction_apport_gratuit_ch(): float
    {
        return $this->xml()->findOneOrError('//apport_et_besoin/fraction_apport_gratuit_ch')->floatval();
    }

    public function fraction_apport_gratuit_depensier_ch(): float
    {
        return $this->xml()->findOneOrError('//apport_et_besoin/fraction_apport_gratuit_depensier_ch')->floatval();
    }
}
