<?php

namespace App\Database\Local;

trait XMLTableRepositoryTrait
{
    protected static ?XMLTableCache $cache = null;
    protected static ?XMLTableElement $db = null;
    protected string $query = "";

    public readonly string $path;

    public function __construct(string $projectDir)
    {
        $table = static::table();
        $this->path = $projectDir . "/etc/tables/{$table}";
    }

    abstract public static function table(): string;

    public function path(): string
    {
        return $this->path;
    }

    public function instance(): XMLTableElement
    {
        if (null === static::$db) {
            static::$db = \simplexml_load_file($this->path() . '.xml', XMLTableElement::class, LIBXML_NOCDATA);
        }
        return static::$db;
    }

    /** @return XMLTableElement[] */
    private function execute(): array
    {
        if (false === static::cache()->has($this->query)) {
            static::cache()->set($this->query, $this->instance()->xpath($this->query) ?? []);
        }
        return static::cache()->get($this->query);
    }

    protected function getOne(): ?XMLTableElement
    {
        $collection = $this->execute();
        return \count($collection) ? \reset($collection) : null;
    }

    /** @return XMLElement[] */
    protected function getMany(): array
    {
        return $this->execute();
    }

    protected function createQuery(): static
    {
        $this->query = "//";
        return $this;
    }

    protected function and(string $attribute, null|string|int|float|bool $value, bool $optional = false): static
    {
        if ($this->query === '//')
            $this->query .= 'row';

        if (\is_bool($value))
            $value = (int) $value;
        if (null === $value)
            $value = '';

        $expression = $optional
            ? \str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute = "$value" or $attribute = ""')
            : \str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute = "$value"');

        $this->query .= "[{$expression}]";
        return $this;
    }

    protected function andCompareTo(string $attribute, null|int|float $value): static
    {
        if ($this->query === '//')
            $this->query .= 'row';

        if (null === $value)
            $value = '""';

        $query = \str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@lt = "" or $attribute/@lt > $value');
        $this->query .= "[{$query}]";
        $query = \str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@lte = "" or $attribute/@lte >= $value');
        $this->query .= "[{$query}]";
        $query = \str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@gt = "" or $attribute/@gt < $value');
        $this->query .= "[{$query}]";
        $query = \str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@gte = "" or $attribute/@gte <= $value');
        $this->query .= "[{$query}]";
        //$query = \str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@e = "" or $attribute/@e = "$value"');
        //$this->query .= "[{$query}]";
        return $this;
    }

    protected static function cache(): XMLTableCache
    {
        if (null === static::$cache) {
            static::$cache = new XMLTableCache;
        }
        return static::$cache;
    }
}
