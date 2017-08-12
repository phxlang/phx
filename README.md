# PHX

PHX (**PH**P Ne**X**t) is a language extension of PHP. While PHP is fully supported by PHX it adds some additional syntax to the existing syntax set of PHP which increases productivity and readability of common PHP code.

## Installation

Install PHX simply by adding the dependency to the project's `composer.json`.

```
composer require phx/phx
```

## Usage

### Standalone

There's a `phx` binary which takes a file path as the first argument which will be the PHX script to be executed:

```bash
$ vendor/bin/phx your_script.phx
```

### With Composer

If you want to use PHX with `composer` autoloading please install the `phx/composer-phx-plugin`.
It will add PHX autoloading support to composer.