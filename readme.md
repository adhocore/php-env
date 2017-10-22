## adhocore/env [![build status](https://travis-ci.org/adhocore/env.svg?branch=master)](https://travis-ci.org/adhocore/env)

- Environment variable loader and retriever for PHP.
- Sanitization/Filters can be applied on retrieval if `filter` extension is loaded.

## Installation
```
composer require adhocore/env
```

## Usage
```php
use Ahc\Env\Loader;
use Ahc\Env\Retriever;

// Load env variables from .env file to `putenv` by default:
(new Loader)->load('/project/root/.env');

// Pass in boolean second param to control if the env should be reloaded:
(new Loader)->load('/project/root/.env', true);

// Load to $_SERVER global:
(new Loader)->load('/project/root/.env', true, Loader::SERVER);

// Load to $_ENV global:
(new Loader)->load('/project/root/.env', true, Loader::ENV);

// Load to all targets:
(new Loader)->load('/project/root/.env', true, Loader::ALL);

// Retrieve:
echo Retriever::getEnv($key);

// Default value:
echo Retriever::getEnv('PAYMENT_GATEWAY', 'stripe');

// Sanitization (pass third and optionally fourth parameters):
echo Retriever::getEnv('MYSQL_PORT', 3306, FILTER_VALIDATE_INT);

// Or you can use `env()` which is alias of `Retriever::getEnv()`:
echo env('THE_KEY');
```

See [filter_var](http://php.net/filter_var) for more on sanitizing/filtering values!

