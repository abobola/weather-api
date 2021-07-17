<?php

declare(strict_types=1);

namespace App\Tests\Forecast\Infrastructure;

use App\Forecast\Application\FindForecastQuery;
use App\Forecast\Application\FindForecastResponse;
use App\Forecast\Infrastructure\FindForecastHandler;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Serializer\Encoder\JsonDecode;

class FindForecastHandlerTest extends TestCase
{
    /** @var ClientInterface&MockObject */
    private ClientInterface | MockObject $weatherApiClient;

    private FindForecastHandler $handler;

    public function setUp(): void
    {
        $this->weatherApiClient = $this->createMock(ClientInterface::class);

        $this->handler = new FindForecastHandler(
            $this->weatherApiClient,
            new JsonDecode([JsonDecode::ASSOCIATIVE => true]),
            new PropertyAccessor()
        );
    }

    /**
     * @dataProvider provideForecast
     */
    public function testHandle(string $forecast): void
    {
        $this->assertJson($forecast);

        $query = new FindForecastQuery(45.84, 6.86, 2);

        $this->weatherApiClient->method('getConfig')
            ->with(RequestOptions::QUERY)
            ->willReturn(['key' => 'api-key']);

        $weatherApiRequest = new Request('GET', 'v1/forecast.json');
        $weatherApiQuery = ['key' => 'api-key', 'q' => '45.84,6.86', 'days' => 2];

        $weatherApiResponse = $this->createMock(ResponseInterface::class);
        $weatherApiResponse->method('getBody')->willReturn($forecast);

        $this->weatherApiClient->method('send')
            ->with($weatherApiRequest, [RequestOptions::QUERY => $weatherApiQuery])
            ->willReturn($weatherApiResponse);

        $result = $this->handler->__invoke($query);
        $this->assertInstanceOf(FindForecastResponse::class, $result);

        $forecast = json_decode($forecast, true);
        $forecastDays = $forecast['forecast']['forecastday'];
        $this->assertCount(count($forecastDays), $result->getForecasts());

        foreach ($result->getForecasts() as $day => $forecast) {
            $this->assertSame($day, $forecast->getDay());
            $this->assertSame(
                $forecastDays[$day]['day']['condition']['text'],
                (string) $forecast->getCondition()
            );
        }
    }

    /**
     * @return array<int, array{string}>
     */
    public function provideForecast(): array
    {
        return [
            ['{"forecast":{"forecastday":[{"day":{"condition":{"text":"Patchy rain possible"}}},{"day":{"condition":{"text":"Patchy rain possible"}}}]}}'],
            ['{"forecast":{"forecastday":[{"day":{"condition":{"text":"Patchy rain possible"}}},{"day":{"condition":{"text":"Partly cloudy"}}}]}}'],
        ];
    }
}
