<?php

namespace App\Services\ExpressionResolver;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class SymfonyExpressionLanguage extends ExpressionLanguage
{
    public function __construct(?CacheItemPoolInterface $cache = null, array $providers = [])
    {
        array_unshift($providers, new SymfonyExpressionFunctionProvider());
        parent::__construct($cache, $providers);
    }
}
