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
use Covex\Environment\Configurator\DiffConfigurator;

class DiffConfiguratorTest extends ConfiguratorTestCase
{
    public function testError(): void
    {
        $this->expectException(ConfiguratorException::class);
        $this->expectExceptionMessage('Diff lines can start with - or + only');

        $configurator = new DiffConfigurator();
        $configurator
            ->setSource('vfs://source.error.txt')
            ->setTarget('vfs://target.txt')
            ->apply();
    }

    public function testClear(): void
    {
        $configurator = new DiffConfigurator();
        $configurator
            ->setSource('vfs://source.clear.txt')
            ->setTarget('vfs://target.txt')
            ->apply();

        $this->assertEquals('', file_get_contents('vfs://target.txt'));
    }

    public function testRemove(): void
    {
        $configurator = new DiffConfigurator();
        $configurator
            ->setSource('vfs://source.remove.txt')
            ->setTarget('vfs://target.txt')
            ->apply();

        $this->assertEquals('password=asdf'.PHP_EOL, file_get_contents('vfs://target.txt'));
    }

    public function testAdd(): void
    {
        $configurator = new DiffConfigurator();
        $configurator
            ->setSource('vfs://source.add.txt')
            ->setTarget('vfs://target.txt')
            ->apply();

        $content = implode(PHP_EOL, [
            '.env', 'asdfgh', 'login=qwerty', 'password=asdf', '',
        ]);
        $this->assertEquals($content, file_get_contents('vfs://target.txt'));
    }

    public function testNewFile(): void
    {
        $this->assertFileNotExists('vfs://.gitattributes');

        $configurator = new DiffConfigurator();
        $configurator
            ->setSource('vfs://source.new.txt')
            ->setTarget('vfs://.gitattributes')
            ->apply();

        $this->assertFileExists('vfs://.gitattributes');
        $this->assertEquals('* text=auto'.PHP_EOL, file_get_contents('vfs://.gitattributes'));
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'diff';
    }
}
