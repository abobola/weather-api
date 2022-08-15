- **Project**: [Musement | Backend tech homework](https://gist.github.com/hpatoio/3aeea8159fb9046a2feba75d39a8d21e)
- **Author**: Anna Bobola, Senior Software Developer at [Boldare](https://www.boldare.com/) 

<!-- TABLE OF CONTENTS -->

## Table of Contents
- [Step 1 | Development](#step-1--development)
    * [Setup](#setup)
    * [Usage](#usage)
    * [Dev Tools](#dev-tools)
- [Step 2 | API Design](#step-2--api-design)

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

#### Configuration
```dotenv
# .env.local

API_WEATHER_KEY=c6a3df043f7b202f4199c956c95673ec # provide your API key from the https://www.weatherapi.com/
```
After changes, compile .env files to .env.local.php:

```bash
root@bfdb629f9356:/app# composer dump-env
```
### Usage

```bash
root@bfdb629f9356:/app# ./bin/console app:fetch-cities-forecasts
```

### Dev Tools

#### Coding Standards

```bash
root@f19009b07593:/app# ./bin/php-cs-fixer fix # run PHP CS Fixer
root@f19009b07593:/app# ./bin/php-cs-fixer fix --dry-run # run the fixer without making changes
```

#### Unit tests
```bash
root@f19009b07593:/app# ./bin/phpunit # run PHPUnit
root@f19009b07593:/app# XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html var/coverage-report # run PHPUnit with coverage report
```

#### Static code analysis
```bash
root@f19009b07593:/app# ./bin/phpstan analyse # run PHPStan
```

## Step 2 | API Design

- **Option 1**: `GET|PUT /weather` (recommended)
- **Option 2**: `GET|PUT /cities/{cityId}/weather`

First option gives us more flexibility and can be easily modified without breaking BC, 
if any business requirements would be changed.
For example, we may:
- add geographical coordinates into DB and allow fetching forecasts within a given radius,
- add `regionId` into DB and allow searching by it,
- add ability to search by a given weather condition.

We may use cron and any queue system, to replace all records in the DB at least once per day, 
in order to keep forecasts up to date.

### Option 1

`PUT /api/v3/weather` set the forecast for a specific city and day

Every time, when new forecast (with a date, generated based on the `day` from the request) is added into the DB, 
outdated forecast will be removed. A forecast for given location per date should be unique.

- **Request Body:**
  - Schema:
    ```json
    {
      "cityId*": "int",
      "day*": "int",
      "condition*": "string"
    }
    ```
  - Example value:
    ```json
    {
      "cityId": 7,
      "day": 0,
      "condition": "Sunny"
    }
    ```
- **Responses:**
  - 204 Forecast saved
  - 400 Incorrect request
     ```json
      {
        "cityId": "This value should not be blank."
      }
      ```
  - 403 Access Denied

`GET /api/v3/weather` get forecasts for a specific search criteria (city) in a limited days period.
Without specified number of days, forecast for 1 day (today) will be returned.

- **Parameters *(query)*:**
    ```json
    {
      "cityId": "int",
      "days": "int"
    }
    ```
  
- **Responses:**
  - 200 Returns search result
    ```json
      [
        {
          "cityId":7,
          "day": 0,
          "condition": "Sunny"
        },
        {
          "cityId":7,
          "day": 1,
          "condition": "Patchy rain possible"
        }
      ]
    ```
  - 400 Incorrect request
    ```json
      {
        "cityId": "This value is incorrect."
      }
     ```