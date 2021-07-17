<?php

declare(strict_types=1);

namespace App\Forecast\Infrastructure;

use App\Forecast\Application\FindForecastQuery;
use App\Forecast\Application\FindForecastResponse;
use App\Forecast\Domain\Condition;
use App\Forecast\Domain\Forecast;
use App\Shared\Application\MessageHandler;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @see https://www.weatherapi.com/docs/
 */
class FindForecastHandler implements MessageHandler
{
    public function __construct(
        private ClientInterface $weatherApiClient,
        private DecoderInterface $decoder,
        private PropertyAccessorInterface $propertyAccessor
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function __invoke(FindForecastQuery $query): FindForecastResponse
    {
        $request = new Request('GET', 'v1/forecast.json');
        $response = $this->weatherApiClient->send($request, [
            RequestOptions::QUERY => array_merge($this->weatherApiClient->getConfig(RequestOptions::QUERY), [
                'q' => $query->getLatitude().','.$query->getLongitude(),
                'days' => $query->getDays(),
            ]),
        ]);

        return $this->handle(
            $this->decoder->decode(
                (string) $response->getBody(), JsonEncoder::FORMAT
            )
        );
    }

    /**
     * @param array{
     *     'forecast': array{'forecastday': array<int, array{
     *         'day': array{'condition': array{'text': string}}
     *     }>}
     * } $response
     */
    private function handle(array $response): FindForecastResponse
    {
        $forecastDays = $this->propertyAccessor->getValue($response, '[forecast][forecastday]');

        $forecasts = [];
        foreach ($forecastDays as $day => $forecast) {
            $value = $this->propertyAccessor->getValue($forecast, '[day][condition][text]');
            $forecasts[] = new Forecast($day, new Condition($value));
        }

        return new FindForecastResponse($forecasts);
    }
}
