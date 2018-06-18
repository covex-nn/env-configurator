# Environment Configurator

Bootstrap new PHP applications with templates

## Install

Environment configurator is a composer plugin. It is recomended to install it globaly:

```bash
composer global require covex-nn/environment
```

## Usage

```
Usage:
  composer env:apply <sequence> [<target>]

Arguments:
  sequence  Sequence name
  target    Target directory [default: "getcwd()"]
```

## Sequence repository

Try already existing repository with `composer global require covex-nn/environment-repository`
or create your own composer package, add the following to your `composer.json`:

```json
{
    "provide": {
        "environment-repository": "^1.0"
    },
    "extra": {
        "environment-repository": "src/Resources/repository"
    }
}
```

... where `extra.environment-repository` is a directory with sequences repository.
Register you package in packagist, require a new package globaly with `composer global`.
See how a [defalt repository](src/Resources/repository) is created here and create and
use your own sequences to bootstrap your own applications.

## Configurators

| Name | Description |
|------:|------------|
| composer | Merge source json file with `composer.json` |
| copy   | Copy source file to target file |
| diff | Source is text file. If a line begins with `+`, a new line will be added, if with `-`, then a line will be removed. A line with `-` only, target file will be truncated |
| docker-compose | Merge source docker-compose target file into target file | 
| replace | Source is a key-value YAML file. `replace` configurator replace key with value in target file. |
| yaml | Merges source yaml into target yaml file. |
