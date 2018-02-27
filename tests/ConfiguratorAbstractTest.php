<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\ServiceConfigurator\Tests;

use Covex\ServiceConfigurator\ConfiguratorAbstract;
use PHPUnit\Framework\TestCase;

class ConfiguratorAbstractTest extends TestCase
{
    public function testSettersGetters(): void
    {
        $configurator = new class() extends ConfiguratorAbstract {
            public function apply(): void
            {
            }
        };

        $this->assertNull($configurator->getSource());
        $this->assertEquals($configurator, $configurator->setSource('asdf'));
        $this->assertEquals('asdf', $configurator->getSource());

        $this->assertNull($configurator->getTarget());
        $this->assertEquals($configurator, $configurator->setTarget('qwerty'));
        $this->assertEquals('qwerty', $configurator->getTarget());
    }
}
