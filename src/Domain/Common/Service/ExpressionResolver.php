<?php

namespace App\Domain\Common\Service;

interface ExpressionResolver
{
    /**
     * Évalue une expression
     * 
     * @return float Le résultat de l'expression
     * @return false Si l'expression est invalide
     */
    public function evalue(string $expression, array $variables = []): float|false;
}
