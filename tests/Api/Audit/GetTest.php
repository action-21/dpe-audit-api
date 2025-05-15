<?php

namespace App\Tests\Api\Audit;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Api\Audit\Model\Audit;

final class GetTest extends ApiTestCase
{
    public function testGet(): void
    {
        $data = static::createClient()
            ->request('GET', '/audits', ['query' => ['randomize' => true]])
            ->toArray()['member'][0];

        $id = $data['id'];
        static::createClient()->request('GET', "/audit/{$id}");

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertMatchesResourceCollectionJsonSchema(Audit::class);
    }
}
