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
use Covex\Environment\Configurator\ConfiguratorInterface;
use Covex\Environment\Configurator\CopyConfigurator;
use Covex\Environment\Configurator\SequenceManager;

class SequenceManagerTest extends VfsTestCase
{
    public function testAddConfigurator(): void
    {
        $manager = new SequenceManager();

        $configurator = new class() implements ConfiguratorInterface {
            public function apply(string $source, string $target): void
            {
            }
        };

        $this->assertFalse($manager->hasConfigurator('qwerty'));
        $manager->addConfigurator('qwerty', $configurator);
        $this->assertTrue($manager->hasConfigurator('qwerty'));
        $this->assertEquals($configurator, $manager->getConfigurator('qwerty'));
    }

    /**
     * @dataProvider providerBadRepository
     */
    public function testBadRepository($repository): void
    {
        $this->expectException(ConfiguratorException::class);

        $manager = new SequenceManager();
        $manager->addRepository($repository);
    }

    public function providerBadRepository(): array
    {
        return [
            [__FILE__],
            [__DIR__.DIRECTORY_SEPARATOR.'not-exists'],
        ];
    }

    public function testAddRepository(): void
    {
        $manager = new SequenceManager();
        $this->assertSame([], $manager->getRepositories());

        $manager->addRepository('vfs://repo1');
        $this->assertSame(['vfs://repo1'], $manager->getRepositories());
        $manager->addRepository('vfs://repo2');
        $this->assertSame(['vfs://repo1', 'vfs://repo2'], $manager->getRepositories());
    }

    public function testRequire(): void
    {
        $manager = new SequenceManager();
        $manager
            ->addRepository('vfs://repo1')
            ->addRepository('vfs://repo2')
            ->addRepository('vfs://repo3')
            ->addConfigurator('copy', new CopyConfigurator());

        $manager->requirePackage('asdf', 'vfs://target');

        $this->assertFileExists('vfs://target/target1.txt');
        $this->assertEquals('source-from-repo2', file_get_contents('vfs://target/target1.txt'));
        $this->assertFileExists('vfs://target/target2.txt');
        $this->assertEquals('source-from-repo3', file_get_contents('vfs://target/target2.txt'));
    }

    protected function getVfsRoot(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'repository';
    }
}
