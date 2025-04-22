<?php

namespace App\Database\Local;

use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class XMLTableDatabase
{
    private string $name;

    public function __construct(
        private string $projectDir,
        private CacheInterface $cache,
    ) {}

    public function repository(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    private function table(): XMLTableElement
    {
        $key = "xmltable::{$this->name}";

        $xml = $this->cache->get($key, function (CacheItemInterface $item) {
            if (false === $item->isHit()) {
                $file = $this->projectDir . "/etc/tables/xml/{$this->name}.xml";

                if (false === file_exists($file)) {
                    throw new \RuntimeException("File not found: {$file}");
                }
                if (false === $content = file_get_contents($file)) {
                    throw new \RuntimeException("Failed to load XML content: {$file}");
                }
                $item->set($content);
                return $item->get();
            }
        });

        return self::stringToXML($xml);
    }

    private function execute(XMLTableQueryBuilder $query): XMLTableCollection
    {
        $key = \sha1($query->getQuery());
        $key = "xmltable::{$this->name}::{$key}";

        /** @var string[] */
        $values = $this->cache->get($key, function (CacheItemInterface $item) use ($query) {
            if (false === $item->isHit()) {
                if (!$result = $this->table()->xpath($query->getQuery())) {
                    throw new \RuntimeException("Error on query {$query->getQuery()}");
                }
                $datas = array_map(fn(XMLTableElement $xml): string => self::XMLToString($xml), $result);
                $item->set($datas);
            }
            return $item->get();
        });

        return new XMLTableCollection(array_map(
            fn(string $data): XMLTableElement => self::stringToXML($data),
            $values,
        ));
    }

    public function createQuery(): XMLTableQueryBuilder
    {
        return new XMLTableQueryBuilder($this);
    }

    public function getOne(XMLTableQueryBuilder $query): ?XMLTableElement
    {
        return $this->execute($query)->first();
    }

    public function getMany(XMLTableQueryBuilder $query): XMLTableCollection
    {
        return $this->execute($query);
    }

    private static function XMLToString(XMLTableElement $xml): string
    {
        return $xml->asXML();
    }

    private static function stringToXML(string $xml): XMLTableElement
    {
        $xml = simplexml_load_string($xml, XMLTableElement::class, LIBXML_NOCDATA);

        if (false === $xml) {
            throw new \RuntimeException("Failed to load XML content");
        }
        return $xml;
    }
}
