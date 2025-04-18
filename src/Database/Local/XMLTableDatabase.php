<?php

namespace App\Database\Local;

final class XMLTableDatabase
{
    /**
     * @var XMLTableElement[]
     */
    private static array $db = [];

    public readonly string $storage;

    public function __construct(private string $projectDir)
    {
        $this->storage = $projectDir . "/etc/tables/xml/";
    }

    public function db(): array
    {
        return static::$db;
    }

    public function repository(string $name): XMLTableRepository
    {
        if (false === \array_key_exists($name, static::$db)) {
            $content = \simplexml_load_file($this->storage . $name . '.xml', XMLTableElement::class, LIBXML_NOCDATA);

            if (false === $content) {
                throw new \RuntimeException("Failed to load XML file: {$this->storage}{$name}.xml");
            }
            static::$db[$name] = $content;
        }
        return new XMLTableRepository(table: static::$db[$name]);
    }
}
