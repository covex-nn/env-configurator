<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Tests\Configurator;

use Covex\Environment\Configurator\DockerComposeConfigurator;
use Covex\Environment\Configurator\DockerComposeDefinition;
use Covex\Environment\Tests\VfsTestCase;

class DockerComposeConfiguratorTest extends VfsTestCase
{
    public function testCreateNew(): void
    {
        $configurator = new DockerComposeConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://new.yaml');

        $data = (new DockerComposeDefinition('vfs://new.yaml'))
            ->getData();

        $this->assertSame([
            'version' => '3.2',
            'services' => [
                'mysql' => [
                    'image' => 'mysql:5.7',
                    'networks' => ['default', 'another'],
                    'volumes' => ['database:/var/lib/mysql'],
                ],
                'php' => [
                    'image' => 'php:latest',
                    'networks' => ['default'],
                    'volumes' => ['another:/tmp'],
                ],
            ],
            'networks' => [
                'default' => ['driver' => 'foo'],
                'another' => [],
                'not_used' => [],
            ],
            'volumes' => [
                'database' => ['driver' => 'bar'],
                'another' => [],
                'not_used' => [],
            ],
        ], $data);
    }

    public function testOverrideVersion(): void
    {
        $configurator = new DockerComposeConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://destination-version.yaml');

        $definition = new DockerComposeDefinition('vfs://destination-version.yaml');
        $this->assertEquals('3.6', $definition->getVersion());
    }

    public function testOverrideVolumes(): void
    {
        $configurator = new DockerComposeConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://destination-volumes.yaml');

        $data = (new DockerComposeDefinition('vfs://destination-volumes.yaml'))
            ->getData();
        $this->assertSame([
            'database' => [],
            'another' => [],
            'not_used' => [],
        ], $data['volumes']);
    }

    public function testOverrideNetworks(): void
    {
        $configurator = new DockerComposeConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://destination-networks.yaml');

        $data = (new DockerComposeDefinition('vfs://destination-networks.yaml'))
            ->getData();
        $this->assertSame([
            'default' => [],
            'another' => [],
            'not_used' => [],
        ], $data['networks']);
    }

    public function testOverrideServiceNoNetworks(): void
    {
        $configurator = new DockerComposeConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://destination-service.yaml');

        $data = (new DockerComposeDefinition('vfs://destination-service.yaml'))
            ->getData();
        $this->assertSame([
            'version' => '3.2',
            'services' => [
                'mysql' => [
                    'image' => 'mysql:5.7',
                ],
                'php' => [
                    'image' => 'php:latest',
                    'networks' => ['default'],
                    'volumes' => ['another:/tmp'],
                ],
            ],
            'networks' => [
                'default' => [
                    'driver' => 'foo',
                ],
                'another' => [],
                'not_used' => [],
            ],
            'volumes' => [
                'database' => [
                    'driver' => 'bar',
                ],
                'another' => [],
                'not_used' => [],
            ],
        ], $data);
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'docker-compose';
    }
}
