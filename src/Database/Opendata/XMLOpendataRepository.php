<?php

namespace App\Database\Opendata;

use App\Domain\Common\ValueObject\Id;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class XMLOpendataRepository
{
    private static array $cache = [];

    public function __construct(
        private readonly HttpClientInterface $client,
    ) {}

    public function find(Id $id): ?XMLElement
    {
        if (isset(self::$cache[$id->value]))
            return self::$cache[$id->value];

        $response = $this->client->request('GET', "https://observatoire-dpe-audit.ademe.fr/pub/dpe/{$id}/zip");

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $temp = tempnam(sys_get_temp_dir(), 'zip');
        file_put_contents($temp, $response->getContent());
        $zip = new \ZipArchive();

        if ($zip->open($temp) !== true) {
            unlink($temp);
            throw new \RuntimeException('Failed to open the zip file');
        }
        if (false === $content = $zip->getFromIndex(0)) {
            $zip->close();
            unlink($temp);
            throw new \RuntimeException('Failed to extract the XML file');
        }
        $xml = \simplexml_load_string($content, XMLElement::class);
        static::$cache[$id->value] = $xml;

        $zip->close();
        unlink($temp);

        return $xml;
    }
}
