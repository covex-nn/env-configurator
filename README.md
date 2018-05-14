Environment Configurator
===

Configure environment with templates

Docker Compose
---

Merge source docker-compose file into target docker-compose file.

```php
use Covex\Environment\Configurator\DockerComposeConfigurator;

$configurator = new DockerComposeConfigurator();
$configurator
    ->setSource('docker-compose.template.yaml')
    ->setTarget('docker-compose.yaml')
    ->apply();
```

Copy
---

Copy source file to target dir.

```php
use Covex\Environment\Configurator\CopyConfigurator;

$configurator = new CopyConfigurator();
$configurator
    ->setSource('file.txt')
    ->setTarget('target/dir')
    ->apply();
```

Replace
---

Source is a key-value YAML file. ReplaceConfigurator replace key with value in target file.

```php
use Covex\Environment\Configurator\ReplaceConfigurator;

$configurator = new ReplaceConfigurator();
$configurator
    ->setSource('changes.yaml')
    ->setTarget('phpunit.xml.dist')
    ->apply();
```

Diff
---

Source is text file. If a line begins with `+`, a new line will be added, if with `-`, then
a line will be removed. A line with `-` only, target file will be truncated.

```php
use Covex\Environment\Configurator\DiffConfigurator;

$configurator = new DiffConfigurator();
$configurator
    ->setSource('changes.txt')
    ->setTarget('.env')
    ->apply();
```

Yaml
---

Merges source yaml into target yaml file.

```php
use Covex\Environment\Configurator\YamlConfigurator;

$configurator = new YamlConfigurator();
$configurator
    ->setSource('templating.yaml')
    ->setTarget('config/packages/framework.yaml')
    ->apply();
```
