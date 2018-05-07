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
use Covex\Environment\Configurator\ReplaceConfigurator;

class ReplaceConfiguratorTest extends ConfiguratorTestCase
{
    public function testNotScalarData(): void
    {
        $this->expectException(ConfiguratorException::class);
        $this->expectExceptionMessage('Source must be an array in vfs://non-scalar-data.yaml');

        $configurator = new ReplaceConfigurator();
        $configurator
            ->setSource('vfs://non-scalar-data.yaml')
            ->setTarget('vfs://target.txt')
            ->apply();
    }

    public function testNotScalarValue(): void
    {
        $this->expectException(ConfiguratorException::class);
        $this->expectExceptionMessage("Replace value for 'qwe' key must be scalar in vfs://non-scalar-value.yaml");

        $configurator = new ReplaceConfigurator();
        $configurator
            ->setSource('vfs://non-scalar-value.yaml')
            ->setTarget('vfs://target.txt')
            ->apply();
    }

    public function testTargetNotFound(): void
    {
        $this->expectException(ConfiguratorException::class);
        $this->expectExceptionMessage('Target file vfs://not-found.txt not found');

        $configurator = new ReplaceConfigurator();
        $configurator
            ->setSource('vfs://source.yaml')
            ->setTarget('vfs://not-found.txt')
            ->apply();
    }

    public function testApply(): void
    {
        $configurator = new ReplaceConfigurator();
        $configurator
            ->setSource('vfs://source.yaml')
            ->setTarget('vfs://target.txt')
            ->apply();

        $this->assertEquals(
            '1_0', file_get_contents('vfs://target.txt')
        );
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'replace';
    }
}
