<?php

namespace App\Domain\Common\Error;

final class EngineTableError extends \DomainException
{
    public function __construct(string $table)
    {
        parent::__construct("La valeur de la table {$table} ne peut pas être déterminée");
    }
}
