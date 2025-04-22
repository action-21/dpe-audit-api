<?php

namespace App\Domain\Common;

use App\Domain\Audit\Audit;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.engine_rule')]
abstract class EngineRule
{
    private array $cache = [];

    public function get(string $name, callable $callback): mixed
    {
        return $this->cache[$name] ?? $this->cache[$name] = $callback();
    }

    public function clear(): static
    {
        $this->cache = [];
        return $this;
    }

    abstract public function apply(Audit $entity): void;

    /**
     * @return string[]
     */
    public static function dependencies(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    final public static function registre(array &$visited = []): array
    {
        $classname = static::class;

        if (in_array($classname, $visited, true)) {
            throw new \LogicException("Dépendance circulaire détectée pour la classe : $classname");
        }
        $visited[] = $classname;
        $registre = [$classname];

        foreach (static::dependencies() as $dependency) {
            if ($dependency === $classname) {
                throw new \LogicException("Self-dependency detected for class: $classname");
            }

            $registre = array_merge($registre, $dependency::registre($visited));
        }

        array_pop($visited);

        return [...array_unique($registre)];
    }

    public static function priority(): int
    {
        $priority = 0;
        $registre = static::registre();

        for ($i = 0; $i < count($registre); $i++) {
            $priority--;
        }
        return $priority;
    }
}
