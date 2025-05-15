<?php

namespace App\Tests\Api\Audit;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Api\Audit\Collection\Audit;

final class GetCollectionTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/audits');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(100, $response->toArray()['member']);
        $this->assertMatchesResourceCollectionJsonSchema(Audit::class);
    }

    public function testGetCollectionWithFilters(): void
    {
        $response = static::createClient()->request('GET', '/audits', [
            'query' => [
                'surface_habitable_min' => 50,
                'surface_habitable_max' => 150,
                'annee_construction_min' => 2000,
                'annee_construction_max' => 2020,
            ]
        ]);

        foreach ($response->toArray()['member'] as $item) {
            $this->assertGreaterThanOrEqual(50, $item['batiment']['surface_habitable']);
            $this->assertLessThanOrEqual(150, $item['batiment']['surface_habitable']);
            $this->assertGreaterThanOrEqual(2000, $item['batiment']['annee_construction']);
            $this->assertLessThanOrEqual(2020, $item['batiment']['annee_construction']);
        }
    }
}
