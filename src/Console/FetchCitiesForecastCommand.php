<?php

declare(strict_types=1);

namespace App\Console;

use App\Forecast\Application\FindCitiesQuery;
use App\Forecast\Application\FindCitiesResponse;
use App\Forecast\Application\TextForecastProvider;
use App\Forecast\Domain\Location;
use App\Shared\Application\QueryBus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchCitiesForecastCommand extends Command
{
    private const DAYS = 2;

    protected static $defaultName = 'app:fetch-cities-forecasts';

    public function __construct(private QueryBus $queryBus, private TextForecastProvider $forecastProvider)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('Display a list of the cities with a forecast for the next 2 days');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->getCities() as $city) {
            $forecast = $this->forecastProvider->__invoke($city, self::DAYS);

            $output->writeln(sprintf('Processed city %s | %s', (string) $city, $forecast));
        }

        return Command::SUCCESS;
    }

    /**
     * @return Location[]
     */
    private function getCities(): array
    {
        /** @var FindCitiesResponse $response */
        $response = $this->queryBus->ask(new FindCitiesQuery());

        return $response->getCities();
    }
}
