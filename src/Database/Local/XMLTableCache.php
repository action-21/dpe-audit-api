<?php

namespace App\Database\Local;

class XMLTableCache
{
    private array $cache = [];

    /**
     * @param XMLTableElement[] $records
     * @return XMLTableElement[]
     */
    public function set(string $query, array $records): array
    {
        $key = $this->hash($query);
        $this->cache[$key] = $records;

        return $records;
    }

    /**
     * @return XMLTable[]
     */
    public function get(string $query): array
    {
        return $this->has($query) ? $this->cache[$this->hash($query)] : false;
    }

    public function has(string $query): bool
    {
        return \array_key_exists($this->hash($query), $this->cache);
    }

    public function hash(string $query): string
    {
        return \sha1($query);
    }
}
