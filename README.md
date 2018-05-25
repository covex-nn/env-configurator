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

## Sequences

Install `symfony/templating` with `symfony/twig-bundle` and enable `templating` in `config/packages/framework.yaml`:

    composer env:apply templating

Create `docker-compose` local environment:

    composer env:apply docker-compose

Install `docker-composer` environment, equal to [covex-nn/docker-symfony](https://github.com/covex-nn/docker-workflow-symfony)

    composer env:apply docker-ci

## Configurators

| Name | Description |
|------:|------------|
| composer | Merge source json file with `composer.json` |
| copy   | Copy source file to target file |
| diff | Source is text file. If a line begins with `+`, a new line will be added, if with `-`, then a line will be removed. A line with `-` only, target file will be truncated |
| docker-compose | Merge source docker-compose target file into target file | 
| replace | Source is a key-value YAML file. `replace` configurator replace key with value in target file. |
| yaml | Merges source yaml into target yaml file. |
