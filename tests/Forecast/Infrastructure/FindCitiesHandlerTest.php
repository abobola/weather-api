<?php

declare(strict_types=1);

namespace App\Tests\Forecast\Infrastructure;

use App\Forecast\Application\FindCitiesQuery;
use App\Forecast\Application\FindCitiesResponse;
use App\Forecast\Infrastructure\FindCitiesHandler;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class FindCitiesHandlerTest extends TestCase
{
    /** @var ClientInterface&MockObject */
    private ClientInterface | MockObject $musementApiClient;

    private FindCitiesHandler $handler;

    public function setUp(): void
    {
        $this->musementApiClient = $this->createMock(ClientInterface::class);

        $this->handler = new FindCitiesHandler(
            $this->musementApiClient,
            new JsonDecode([JsonDecode::ASSOCIATIVE => true]),
            new PropertyAccessor()
        );
    }

    /**
     * @dataProvider provideCities
     */
    public function testHandle(string $cities): void
    {
        $this->assertJson($cities);

        $musementApiRequest = new Request('GET', 'v3/cities');

        $musementApiResponse = $this->createMock(ResponseInterface::class);
        $musementApiResponse->method('getBody')->willReturn($cities);

        $this->musementApiClient->method('send')
            ->with($musementApiRequest)
            ->willReturn($musementApiResponse);

        $result = $this->handler->__invoke(new FindCitiesQuery());
        $this->assertInstanceOf(FindCitiesResponse::class, $result);

        $cities = json_decode($cities, true);
        $this->assertCount(count($cities), $result->getCities());

        foreach ($result->getCities() as $key => $location) {
            $this->assertSame($cities[$key]['name'], $location->getCity());
            $this->assertSame($cities[$key]['latitude'], $location->getLatitude());
            $this->assertSame($cities[$key]['longitude'], $location->getLongitude());
        }
    }

    /**
     * @return array<int, array{string}>
     */
    public function provideCities(): array
    {
        return [
            ['[{"name":"Amsterdam","latitude":52.374,"longitude":4.9},{"name":"Paris","latitude":48.866,"longitude":2.355}]'],
            ['[{"name":"Rome","latitude":41.898,"longitude":12.483},{"name":"Milan","latitude":45.459,"longitude":9.183}]'],
        ];
    }
}
