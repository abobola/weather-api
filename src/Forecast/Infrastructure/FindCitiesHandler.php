<?php

declare(strict_types=1);

namespace App\Forecast\Infrastructure;

use App\Forecast\Application\FindCitiesQuery;
use App\Forecast\Application\FindCitiesResponse;
use App\Forecast\Domain\Location;
use App\Shared\Application\MessageHandler;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

/**
 * @see https://api.musement.com/swagger_3.5.0.json
 */
class FindCitiesHandler implements MessageHandler
{
    public function __construct(
        private ClientInterface $musementApiClient,
        private DecoderInterface $decoder,
        private PropertyAccessorInterface $propertyAccessor
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function __invoke(FindCitiesQuery $query): FindCitiesResponse
    {
        $request = new Request('GET', 'v3/cities');
        $response = $this->musementApiClient->send($request);

        return $this->handle(
            $this->decoder->decode(
                (string) $response->getBody(), JsonEncoder::FORMAT
            )
        );
    }

    /**
     * @param array<int, array{'name', 'latitude', 'longitude'}> $response
     */
    private function handle(array $response): FindCitiesResponse
    {
        $cities = [];
        foreach ($response as $city) {
            $name = $this->propertyAccessor->getValue($city, '[name]');
            $latitude = $this->propertyAccessor->getValue($city, '[latitude]');
            $longitude = $this->propertyAccessor->getValue($city, '[longitude]');

            $cities[] = new Location($name, $latitude, $longitude);
        }

        return new FindCitiesResponse($cities);
    }
}
