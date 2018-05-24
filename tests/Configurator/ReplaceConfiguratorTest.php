<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Tests\Configurator;

use Covex\Environment\Configurator\ConfiguratorException;
use Covex\Environment\Configurator\ReplaceConfigurator;
use Covex\Environment\Tests\VfsTestCase;

class ReplaceConfiguratorTest extends VfsTestCase
{
    public function testNotScalarData(): void
    {
        $this->expectException(ConfiguratorException::class);
        $this->expectExceptionMessage('Source must be an array in vfs://non-scalar-data.yaml');

        $configurator = new ReplaceConfigurator();
        $configurator->apply('vfs://non-scalar-data.yaml', 'vfs://target.txt');
    }

    public function testNotScalarValue(): void
    {
        $this->expectException(ConfiguratorException::class);
        $this->expectExceptionMessage("Replace value for 'qwe' key must be scalar in vfs://non-scalar-value.yaml");

        $configurator = new ReplaceConfigurator();
        $configurator->apply('vfs://non-scalar-value.yaml', 'vfs://target.txt');
    }

    public function testTargetNotFound(): void
    {
        $this->assertFileNotExists('vfs://file-does-not-exists.txt');

        $configurator = new ReplaceConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://file-does-not-exists.txt');

        $this->assertFileNotExists('vfs://file-does-not-exists.txt');
    }

    public function testApply(): void
    {
        $configurator = new ReplaceConfigurator();
        $configurator->apply('vfs://source.yaml', 'vfs://target.txt');

        $this->assertEquals(
            '1_0', file_get_contents('vfs://target.txt')
        );
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'replace';
    }
}
