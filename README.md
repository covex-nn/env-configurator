Environment Configurator
===

Configure environment with templates

Docker Compose
---

```php
use Covex\Environment\Configurator\DockerComposeConfigurator;

$configurator = new DockerComposeConfigurator();
$configurator
    ->setSource('docker-compose.template.yaml')
    ->setTarget('docker-compose.yaml')
    ->apply();
```
