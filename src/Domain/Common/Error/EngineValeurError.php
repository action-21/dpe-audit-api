<?php

namespace App\Domain\Common\Error;

final class EngineValeurError extends \DomainException
{
    public function __construct(string $valeur)
    {
        parent::__construct("La valeur {$valeur} ne peut pas être déterminée");
    }
}
