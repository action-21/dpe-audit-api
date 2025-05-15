<?php

namespace App\Services\Opendata;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Opendata
{
    final public const URL = 'https://data.ademe.fr/data-fair/api/v1/datasets/dpe03existant';

    private ?Error $error = null;

    public function __construct(
        private readonly HttpClientInterface $client,
    ) {}

    public function lines(array $query): ?Lines
    {
        $path = self::URL . '/lines';

        $response = $this->client->request('GET', $path, [
            'query' => $query,
        ]);
        if ($response->getStatusCode() !== 200) {
            $this->error = new Error(
                code: $response->getStatusCode(),
                message: $response->getContent(false),
            );
            return null;
        }
        $data = $response->toArray();

        return new Lines(
            total: $data['total'],
            next: $data['next'],
            results: $data['results'],
        );
    }

    public function getError(): ?Error
    {
        return $this->error;
    }
}
