<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLElement;
use App\Domain\Audit\Enum\MethodeApplication;
use App\Domain\Audit\Enum\MethodeCalcul;
use App\Domain\Audit\Enum\PerimetreApplication;
use App\Domain\Audit\ValueObject\Auditeur;
use App\Domain\Common\Identifier\Reference;

final class XMLAuditReader
{
    private XMLElement $xml;

    public function id(): \Stringable
    {
        return Reference::create($this->reference());
    }

    public function reference(): string
    {
        return $this->xml->findOneOrError('//numero_dpe')->getValue();
    }

    public function enum_methode_application_dpe_log_id(): int
    {
        return (int) $this->xml->findOneOrError('//enum_methode_application_dpe_log_id')->getValue();
    }

    public function auditeur(): Auditeur
    {
        return new Auditeur;
    }

    // Données déduites

    public function methode_application(): MethodeApplication
    {
        return MethodeApplication::from_enum_methode_application_dpe_log_id($this->enum_methode_application_dpe_log_id());
    }

    public function perimetre_application(): PerimetreApplication
    {
        return PerimetreApplication::from_enum_methode_application_log_id($this->enum_methode_application_dpe_log_id());
    }

    public function methode_calcul(): MethodeCalcul
    {
        return MethodeCalcul::from_enum_methode_application_log_id($this->enum_methode_application_dpe_log_id());
    }

    public function read(XMLElement $xml): self
    {
        $this->xml = $xml;
        return $this;
    }
}
