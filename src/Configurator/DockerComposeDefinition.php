<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

use Symfony\Component\Yaml\Yaml;

/**
 * docker-compose.yaml file representation.
 */
class DockerComposeDefinition implements DefinitionInterface
{
    /**
     * @var array
     */
    private $data;

    public function __construct(string $path)
    {
        $this->data = Yaml::parseFile($path);

        if (isset($this->data['networks'])) {
            foreach ($this->data['networks'] as $name => $value) {
                if (null === $value) {
                    $this->data['networks'][$name] = [];
                }
            }
        }
        if (isset($this->data['volumes'])) {
            foreach ($this->data['volumes'] as $name => $value) {
                if (null === $value) {
                    $this->data['volumes'][$name] = [];
                }
            }
        }
    }

    public function getVersion(): string
    {
        return $this->data['version'];
    }

    /**
     * @return $this
     */
    public function setVersion(string $version): self
    {
        $this->data['version'] = $version;

        return $this;
    }

    public function hasServices(): bool
    {
        return $this->hasData('services');
    }

    public function allServices(): \Generator
    {
        if ($this->hasServices()) {
            foreach (array_keys($this->data['services']) as $name) {
                yield $name;
            }
        }
    }

    public function getService($name): ?array
    {
        return $this->data['services'][$name] ?? null;
    }

    /**
     * @return $this
     */
    public function setService(string $name, array $data): self
    {
        if (!$this->hasServices()) {
            $this->data['services'] = [
                $name => $data,
            ];
        } else {
            $this->data['services'][$name] = $data;
        }

        return $this;
    }

    public function hasVolumes(): bool
    {
        return $this->hasData('volumes');
    }

    public function allVolumes(): \Generator
    {
        if ($this->hasVolumes()) {
            foreach ($this->data['volumes'] as $key => $value) {
                yield is_scalar($value) ? $value : $key;
            }
        }
    }

    /**
     * @return string|array|null
     */
    public function getVolume(string $name)
    {
        $key = $this->getVolumeKey($name);

        return null === $key ? null : $this->data['volumes'][$key];
    }

    /**
     * @param null|string|array $value
     */
    public function setVolume(string $name, $value): self
    {
        if (!$this->hasVolumes()) {
            $this->data['volumes'] = [];
        }

        $key = $this->getVolumeKey($name);
        $this->data['volumes'][$key] = null === $value ? $name : $value;

        return $this;
    }

    public function hasNetworks(): bool
    {
        return $this->hasData('networks');
    }

    public function allNetworks(): \Generator
    {
        if ($this->hasNetworks()) {
            foreach (array_keys($this->data['networks']) as $name) {
                yield $name;
            }
        }
    }

    public function getNetwork(string $name): ?array
    {
        return $this->data['networks'][$name] ?? null;
    }

    public function setNetwork(string $name, ?array $value): self
    {
        if (!$this->hasNetworks()) {
            $this->data['networks'] = [];
        }
        $this->data['networks'][$name] = $value;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function dump(): string
    {
        return Yaml::dump($this->data, 16);
    }

    private function hasData($key): bool
    {
        return isset($this->data[$key]) && count($this->data[$key]);
    }

    private function getVolumeKey(string $name)
    {
        if ($this->hasVolumes()) {
            foreach ($this->data['volumes'] as $key => $value) {
                $volumeName = is_scalar($value) ? $value : $key;
                if ($volumeName === $name) {
                    return $key;
                }
            }
        }

        return null;
    }
}
