## adhocore/env

[![Latest Version](https://img.shields.io/github/release/adhocore/php-env.svg?style=flat-square)](https://github.com/adhocore/php-env/releases)
[![Travis Build](https://travis-ci.org/adhocore/php-env.svg?branch=master)](https://travis-ci.org/adhocore/php-env?branch=master)
[![Scrutinizer CI](https://img.shields.io/scrutinizer/g/adhocore/php-env.svg?style=flat-square)](https://scrutinizer-ci.com/g/adhocore/php-env/?branch=master)
[![Codecov branch](https://img.shields.io/codecov/c/github/adhocore/php-env/master.svg?style=flat-square)](https://codecov.io/gh/adhocore/php-env)
[![StyleCI](https://styleci.io/repos/107715208/shield)](https://styleci.io/repos/107715208)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

- Environment variable loader and retriever for PHP.
- Sanitization/Filters can be applied on retrieval if `filter` extension is loaded.
- Using env to configure application is one of the [12 postulates](https://12factor.net/config).

## Installation
```
composer require adhocore/env
```

## Usage

### Loading

```php
use Ahc\Env\Loader;

// Load env variables from .env file to `putenv` by default:
(new Loader)->load('/project/root/.env');

// Pass in boolean second param to control if the env should be reloaded:
(new Loader)->load('/project/root/.env', true);

// Load to $_SERVER global:
(new Loader)->load('/project/root/.env', true, Loader::SERVER);

// Load to $_ENV global and putenv():
(new Loader)->load('/project/root/.env', true, Loader::ENV | Loader::PUTENV);

// Load to all targets:
(new Loader)->load('/project/root/.env', true, Loader::ALL);
```

> Always wrap complex values within double quotes in `.env` file. Eg: `APP_KEY="K&^¢*&D(?<µ}^(P\]X"`

### ENV Format

Supports `#` or `;` comments. Literal double quote should be escaped like `""`. See more examples below:

```
# comment line
a=1
b="2"
c=$3#
; also comment line
d="lol"
# empty
e=
# f is `"6"`
f=""6""
1_2=one_two
# empty too
E=""
A_B=Apple Ball
x=Y
```

Reference is possible like so:

```
MAIN=1
REF=${MAIN}/2
REF2=${REF}/3
# below will not be parsed as INV is not resolved
REF3=${INV}
```

### Retrieving

```php
use Ahc\Env\Retriever;

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

## Benchmark

If you are interested [here](https://github.com/adhocore/env-bench) is a simple benchmark.

---
### Consideration

By default this library only loads env to `putenv()`.
Be cautious exposing confidential credentials into `$_ENV` and `$_SERVER` which bug/error catchers may log.

Although this libray is already fast enough, in production you might want to boost performance a little by loading if only required:

```php
if (!getenv('<LAST_ENV_APP_SHOULD_BE_AWARE_OF>')) {
    // Override false :)
    (new Loader)->load('/project/root/.env', false);
}
```

For example if your app last introduced `FB_APP_ID` env, but this value is not already hard set in the machine,
it would be loaded via `.env` file else you are already covered.

### Credits

This project is [release](https://github.com/adhocore/php-env/releases)
managed by [please](https://github.com/adhocore/please).
