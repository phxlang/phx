# PHX

PHX (**PH**P Ne**X**t) is a language extension of PHP. While PHP is fully supported by PHX it adds some additional syntax to the existing syntax set of PHP which increases productivity and readability of your code.

## Installation

Install PHX simply by adding the dependecy to your `composer.json`.

```
composer require phx/phx
```

## Usage

### CLI

There's a `phx` binary which takes a file path as the first argument which will be the PHX script to be executed:

```bash
$ vendor/bin/phx your_script.phx
```

### As a composer dependency

If you use PHX as a `composer` dependency, every file which ends in `.phx` will be executed by PHX automatically.