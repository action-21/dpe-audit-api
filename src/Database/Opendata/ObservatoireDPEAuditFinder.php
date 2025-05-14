<?php

namespace App\Database\Opendata;

use App\Domain\Common\ValueObject\Id;
use Psr\Cache\CacheItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class ObservatoireDPEAuditFinder
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly CacheInterface $cache,
    ) {}

    public function find(Id $id): ?XMLElement
    {
        $key = "dpeaudit::{$id}";

        $content = $this->cache->get($key, function (CacheItemInterface $item) use ($id): ?string {
            if (false === $item->isHit()) {
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
                $item->set($content);
                $zip->close();
                unlink($temp);
            }
            return $item->get();
        });
        return \simplexml_load_string($content, XMLElement::class);
    }
}
