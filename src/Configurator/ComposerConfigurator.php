<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

use Composer\Command\BaseCommand;
use Composer\Factory;
use Composer\Installer;
use Composer\Json\JsonManipulator;

class ComposerConfigurator implements ConfiguratorInterface, CommandAwareInterface
{
    /**
     * @var BaseCommand
     */
    private $command;

    private $sections = [
        'require', 'require-dev', 'replaces', 'conflicts',
    ];

    /**
     * @return $this
     */
    final public function setCommand(BaseCommand $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function apply(string $source, string $target): void
    {
        $target = Factory::getComposerFile(); // cannot use 'target://composer.json' =(

        $json = json_decode(file_get_contents($source), true);
        $manipulator = new JsonManipulator(
            file_exists($target) ? file_get_contents($target) : '{}'
        );

        foreach ($this->sections as $section) {
            if (isset($json[$section])) {
                foreach ($json[$section] as $package => $constraint) {
                    $manipulator->addLink($section, $package, $constraint, true);
                }
            }
        }
        file_put_contents($target, $manipulator->getContents());

        $this->command->resetComposer();
        Installer::create($this->command->getIO(), $this->command->getComposer())
            ->setDevMode()
            ->setDumpAutoloader()
            ->setRunScripts()
            ->setSkipSuggest()
            ->setUpdate()
            ->run();
    }
}
