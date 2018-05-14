<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator\Tests;

use Covex\Environment\Configurator\ConfiguratorAbstract;
use PHPUnit\Framework\TestCase;

class ConfiguratorAbstractTest extends TestCase
{
    /**
     * @var ConfiguratorAbstract
     */
    private $configurator;

    protected function setUp(): void
    {
        $this->configurator = new class() extends ConfiguratorAbstract {
            public function apply(): void
            {
            }
        };
    }

    public function testSettersGetters(): void
    {
        $this->assertEquals($this->configurator, $this->configurator->setSource('asdf'));
        $this->assertEquals('asdf', $this->configurator->getSource());

        $this->assertEquals($this->configurator, $this->configurator->setTarget('qwerty'));
        $this->assertEquals('qwerty', $this->configurator->getTarget());
    }

    public function testSourceTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->configurator->getSource();
    }

    public function testTargetTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->configurator->getTarget();
    }
}
