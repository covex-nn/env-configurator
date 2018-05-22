# Environment Configurator

Configure environment with templates

## Install

Environment configurator is a composer plugin. It is recomended to install it globaly:

```bash
composer global require covex-nn/environment
```

## Usage

```
Usage:
  composer env:apply <package> [<target>]

Arguments:
  package   Package name
  target    Target directory [default: "getcwd()"]
```

## Configurators

### composer

Update composer.json

```php
use Covex\Environment\Configurator\ComposerConfigurator;

$configurator = new ComposerConfigurator();
$configurator->apply('composer-phpunit.json', 'composer.json');
```

### copy

Copy source file.

```php
use Covex\Environment\Configurator\CopyConfigurator;

$configurator = new CopyConfigurator();
$configurator->apply('file.txt', 'dir/target.txt');
```

### diff

Source is text file. If a line begins with `+`, a new line will be added, if with `-`, then
a line will be removed. A line with `-` only, target file will be truncated.

```php
use Covex\Environment\Configurator\DiffConfigurator;

$configurator = new DiffConfigurator();
$configurator->apply('changes.txt', '.env');
```

### docker-compose

Merge source docker-compose file into target docker-compose file.

```php
use Covex\Environment\Configurator\DockerComposeConfigurator;

$configurator = new DockerComposeConfigurator();
$configurator->apply('docker-compose.template.yaml', 'docker-compose.yaml');
```

### replace

Source is a key-value YAML file. ReplaceConfigurator replace key with value in target file.

```php
use Covex\Environment\Configurator\ReplaceConfigurator;

$configurator = new ReplaceConfigurator();
$configurator->apply('changes.yaml', 'phpunit.xml.dist');
```

### yaml

Merges source yaml into target yaml file.

```php
use Covex\Environment\Configurator\YamlConfigurator;

$configurator = new YamlConfigurator();
$configurator->apply('templating.yaml', 'config/packages/framework.yaml');
```
