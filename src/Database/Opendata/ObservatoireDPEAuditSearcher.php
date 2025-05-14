<?php

namespace App\Database\Opendata;

use App\Domain\Common\Enum\CodeRegion;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ObservatoireDPEAuditSearcher
{
    public final const METHOD = 'GET';
    public final const URL = 'https://data.ademe.fr/data-fair/api/v1/datasets/dpe03existant/lines';

    private array $query = [];

    private bool $randomize = false;

    public function __construct(
        private readonly HttpClientInterface $client,
    ) {}

    public function randomize(): self
    {
        $this->randomize = true;
        return $this;
    }

    public function prepare(): array
    {
        $this->addQuery('size', 100);
        $this->addQuery('version_dpe_in', ['2.2', '2.3', '2.4']);
        $this->addQuery('code_region_ban_in', array_diff(
            array_column(CodeRegion::cases(), 'value'),
            array_column(CodeRegion::cases_outre_mer(), 'value'),
        ));

        return array_map(
            fn(string|array $value) => is_array($value) ? implode(',', $value) : $value,
            $this->query,
        );
    }

    /**
     * @param string[]|string $value
     */
    public function addQuery(string $key, array|string $value): self
    {
        $this->query[$key] = $value;

        return $this;
    }

    public function fetch(): array
    {
        $response = $this->client->request(self::METHOD, self::URL, [
            'query' => $this->prepare(),
        ]);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Error fetching data from API: ' . $response->getStatusCode());
        }
        return $response->toArray();
    }

    public function search(): array
    {
        return $this->fetch()['results'];
    }

    public function count(): int
    {
        return $this->fetch()['total'];
    }
}
