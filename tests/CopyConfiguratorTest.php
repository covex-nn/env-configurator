<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator\Tests;

use Covex\Environment\Configurator\ConfiguratorException;
use Covex\Environment\Configurator\CopyConfigurator;

class CopyConfiguratorTest extends VfsTestCase
{
    public function testNotDirectory(): void
    {
        $this->expectException(ConfiguratorException::class);
        $this->expectExceptionMessage('vfs://dir is not a directory');

        touch('vfs://dir');

        $configurator = new CopyConfigurator();
        $configurator->apply('vfs://source.txt', 'vfs://dir/target.txt');
    }

    public function testApply(): void
    {
        $this->assertFileNotExists('vfs://dir1/target/source.txt');

        $configurator = new CopyConfigurator();
        $configurator->apply('vfs://source.txt', 'vfs://dir1/target/source2.txt');

        $this->assertFileExists('vfs://dir1/target/source2.txt');
        $this->assertEquals(
            '123', file_get_contents('vfs://dir1/target/source2.txt')
        );
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'copy';
    }
}
