<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\ServiceConfigurator\Tests;

use Covex\ServiceConfigurator\DockerCompose;
use Covex\ServiceConfigurator\DockerCompose\Definition;
use Covex\Stream\FileSystem;
use PHPUnit\Framework\TestCase;

class DockerComposeTest extends TestCase
{
    protected function setUp(): void
    {
        FileSystem::register('vfs', __DIR__.DIRECTORY_SEPARATOR.'docker-compose');
    }

    protected function tearDown(): void
    {
        FileSystem::unregister('vfs');
    }

    public function testCreateNew(): void
    {
        $configurator = new DockerCompose();
        $configurator
            ->setSource('vfs://source.yaml')
            ->setTarget('vfs://new.yaml')
            ->apply();

        $data = Definition::parseFile('vfs://new.yaml')
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
        $configurator = new DockerCompose();
        $configurator
            ->setSource('vfs://source.yaml')
            ->setTarget('vfs://destination-version.yaml')
            ->apply();

        $data = Definition::parseFile('vfs://destination-version.yaml')
            ->getData();
        $this->assertEquals('3.6', $data['version']);
    }

    public function testOverrideVolumes(): void
    {
        $configurator = new DockerCompose();
        $configurator
            ->setSource('vfs://source.yaml')
            ->setTarget('vfs://destination-volumes.yaml')
            ->apply();

        $data = Definition::parseFile('vfs://destination-volumes.yaml')
            ->getData();
        $this->assertSame([
            'database' => [],
            'another' => [],
            'not_used' => [],
        ], $data['volumes']);
    }

    public function testOverrideNetworks(): void
    {
        $configurator = new DockerCompose();
        $configurator
            ->setSource('vfs://source.yaml')
            ->setTarget('vfs://destination-networks.yaml')
            ->apply();

        $data = Definition::parseFile('vfs://destination-networks.yaml')
            ->getData();
        $this->assertSame([
            'default' => [],
            'another' => [],
            'not_used' => [],
        ], $data['networks']);
    }

    public function testOverrideServiceNoNetworks(): void
    {
        $configurator = new DockerCompose();
        $configurator
            ->setSource('vfs://source.yaml')
            ->setTarget('vfs://destination-service.yaml')
            ->apply();

        $data = Definition::parseFile('vfs://destination-service.yaml')
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
}
