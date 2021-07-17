[Musement | Backend tech homework](https://gist.github.com/hpatoio/3aeea8159fb9046a2feba75d39a8d21e)

<!-- TABLE OF CONTENTS -->

## Table of Contents
- [Step 1 | Development](#step-1--development)
    * [Setup](#setup)
    * [Usage](#usage)
    * [Dev Tools](#dev-tools)
- [Step 2 | API Design]

## Step 1 | Development

Application that gets the list of the cities from Musement's API for each city gets the forecast for the next 2 days using http://api.weatherapi.com and print to STDOUT "Processed city [city name] | [weather today] - [wheather tomorrow]"

*Example:*
> Processed city Milan | Heavy rain - Partly cloudy
>
> Processed city Rome | Sunny - Sunny

### Setup

Application contains basic docker configuration **only for development** purpose

#### Prerequisites

- Install Docker Engine and Docker Compose

#### Installation

```bash
$ git clone git@github.com:abobola/weather-api.git . # clone the repository
$ docker-compose exec php bash # connect to the PHP docker container

root@b82b4949e9c9:/app# composer install # install dependencies inside the container
```

### Usage

not implemented yet

### Dev Tools

#### Coding Standards

```bash
root@f19009b07593:/app# ./bin/php-cs-fixer fix # run PHP CS Fixer
root@f19009b07593:/app# ./bin/php-cs-fixer fix --dry-run # run the fixer without making changes
```

#### Unit tests
```bash
root@f19009b07593:/app# ./bin/phpunit # run PHPUnit
root@f19009b07593:/app# ./bin/phpunit -dxdebug.mode=coverage --coverage-html var/coverage-report # run PHPUnit with coverage report
```

#### Static code analysis
```bash
root@f19009b07593:/app# ./bin/phpstan analyse # run PHPStan
```