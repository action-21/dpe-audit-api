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
        $this->path = $projectDir . "/etc/tables/xml/{$table}";
    }

    abstract public static function table(): string;

    public function path(): string
    {
        return $this->path;
    }

    public function instance(): XMLTableElement
    {
        if (null === static::$db) {
            static::$db = \simplexml_load_file($this->path(), XMLTableElement::class, LIBXML_NOCDATA);
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

    protected function and(string $expression): static
    {
        if ($this->query === '//') {
            $this->query .= 'row';
        }
        $this->query .= "[{$expression}]";
        return $this;
    }

    protected function andCompareTo(string $attribute, int|float $value): static
    {
        $this->and(\str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@lt = "" or $attribute/@lt > "$value"'));
        $this->and(\str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@lte = "" or $attribute/@lte >= "$value"'));
        $this->and(\str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@gt = "" or $attribute/@gt < "$value"'));
        $this->and(\str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@gte = "" or $attribute/@gte <= "$value"'));
        $this->and(\str_replace(['$attribute', '$value'], [$attribute, $value], '$attribute/@e = "" or $attribute/@e = "$value"'));
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
