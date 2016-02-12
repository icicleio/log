# Asynchronous Log for Icicle

This library is a component for [Icicle](https://github.com/icicleio/icicle), providing an asynchronous logging interface and implementation based on Icicle's [stream package](https://github.com/icicleio/stream). Like other Icicle components, this library uses [Coroutines](//github.com/icicleio/icicle/wiki/Coroutines) built from [Awaitables](https://github.com/icicleio/icicle/wiki/Awaitables) and [Generators](http://www.php.net/manual/en/language.generators.overview.php) to make writing asynchronous code more like writing synchronous code.

[![Build Status](https://img.shields.io/travis/icicleio/log/v1.x.svg?style=flat-square)](https://travis-ci.org/icicleio/log)
[![Coverage Status](https://img.shields.io/coveralls/icicleio/log/v1.x.svg?style=flat-square)](https://coveralls.io/r/icicleio/log)
[![Semantic Version](https://img.shields.io/github/release/icicleio/log.svg?style=flat-square)](http://semver.org)
[![MIT License](https://img.shields.io/packagist/l/icicleio/log.svg?style=flat-square)](LICENSE)
[![@icicleio on Twitter](https://img.shields.io/badge/twitter-%40icicleio-5189c7.svg?style=flat-square)](https://twitter.com/icicleio)

#### Documentation and Support

- [Full API Documentation](https://icicle.io/docs)
- [Official Twitter](https://twitter.com/icicleio)
- [Gitter Chat](https://gitter.im/icicleio/icicle)

##### Requirements

- PHP 5.5+ for v0.1.x branch (current stable) and v1.x branch (mirrors current stable)
- PHP 7 for v2.0 branch (under development) supporting generator delegation and return expressions

##### Installation

The recommended way to install is with the [Composer](http://getcomposer.org/) package manager. (See the [Composer installation guide](https://getcomposer.org/doc/00-intro.md) for information on installing and using Composer.)

Run the following command to use this library in your project: 

```bash
composer require icicleio/log
```

You can also manually edit `composer.json` to add this library as a project requirement.

```js
// composer.json
{
    "require": {
        "icicleio/log": "^0.1"
    }
}
```
