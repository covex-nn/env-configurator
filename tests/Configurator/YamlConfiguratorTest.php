<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Tests\Configurator;

use Covex\Environment\Configurator\YamlConfigurator;
use Covex\Environment\Tests\VfsTestCase;
use Symfony\Component\Yaml\Yaml;

class YamlConfiguratorTest extends VfsTestCase
{
    public function testApply(): void
    {
        $configurator = new YamlConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://target.yaml');

        $this->assertSame(
            ['root' => ['key1' => 'value1', 'key2' => 'value3', 'key3' => 'value4']],
            Yaml::parseFile('vfs://target.yaml')
        );
    }

    public function testApplyCreate(): void
    {
        $this->assertFileNotExists('vfs://new.yaml');

        $configurator = new YamlConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://new.yaml');

        $this->assertFileExists('vfs://new.yaml');
        $this->assertSame(
            ['root' => ['key2' => 'value3', 'key3' => 'value4']],
            Yaml::parseFile('vfs://new.yaml')
        );
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'yaml';
    }
}
