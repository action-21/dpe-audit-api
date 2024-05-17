<?php

namespace App\Domain\Common\Error;

final class EngineError extends \DomainException
{
    public static function from_variable(string $variable): self
    {
        return new self("Variable {$variable} no définie");
    }

    public static function from_table(string $table): self
    {
        return new self("La valeur de la table {$table} ne peut pas être déterminée");
    }
}
