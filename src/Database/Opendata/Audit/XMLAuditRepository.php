<?php

namespace App\Database\Opendata\Audit;

use App\Database\Opendata\XMLElement;
use App\Domain\Audit\{Audit, AuditRepository};
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class XMLAuditRepository implements AuditRepository
{
    public function __construct(
        private HttpClientInterface $client,
        private XMLAuditParser $audit_parser,
    ) {
    }

    public function save(Audit $audit): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function remove(Audit $audit): void
    {
        throw new \RuntimeException('Not implemented');
    }

    public function find(\Stringable $id): ?Audit
    {
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
        $content = $zip->getFromIndex(0);
        $xml = \simplexml_load_string($content, XMLElement::class);
        $zip->close();
        unlink($temp);
        return $this->audit_parser->parse($xml);
    }
}
