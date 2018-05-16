<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Composer;

use Composer\Command\BaseCommand;
use Covex\Environment\Configurator\ConfiguratorException;
use Covex\Environment\Configurator\ConfiguratorInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ApplyCommand extends BaseCommand
{
    protected const MANIFEST = 'manifest.yaml';

    /**
     * @var ConfiguratorInterface[]
     */
    private $configurators;

    /**
     * @var string[]
     */
    private $repositories;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->configurators = [];
        $this->repositories = [];
    }

    /**
     * @return $this
     */
    public function addConfigurator(string $name, ConfiguratorInterface $configurator): self
    {
        $this->configurators[$name] = $configurator;

        return $this;
    }

    public function hasConfigurator(string $name): bool
    {
        return isset($this->configurators[$name]);
    }

    public function getConfigurator(string $name): ConfiguratorInterface
    {
        if ($this->hasConfigurator($name)) {
            return $this->configurators[$name];
        }
        throw new ConfiguratorException(sprintf('Configurator "%s" does not exists', $name));
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    /**
     * @return $this
     */
    public function addRepository(string $repository): self
    {
        if (!in_array($repository, $this->repositories)) {
            if (!is_dir($repository) || !file_exists($repository)) {
                throw new ConfiguratorException(
                    sprintf('Repository %s is not a directory or does not exists', $repository)
                );
            }
            $this->repositories[] = $repository;
        }

        return $this;
    }

    protected function configure(): void
    {
        $this
            ->setName('env:apply')
            ->setDescription('Apply configuration package')
            ->addArgument('package', InputArgument::REQUIRED, 'Package name')
            ->addArgument('target', InputArgument::OPTIONAL, 'Target directory', getcwd());
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $package = $input->getArgument('package');
        $target = $input->getArgument('target');

        $sequence = [];
        $this->createSequence($package, $sequence);

        foreach ($sequence as $name => $data) {
            $source = $data['source'];
            foreach ($data['sequence'] as $message => $configurators) {
                foreach ($configurators as $name => $files) {
                    $configurator = $this->getConfigurator($name);
                    foreach ($files as $from => $to) {
                        $configurator->apply($source.'/'.$from, $target.'/'.$to);
                    }
                }
            }
        }
    }

    private function createSequence(string $package, array &$sequence): void
    {
        if (isset($sequence[$package])) {
            return;
        }

        $source = $this->getPackageSource($package);
        if (null === $source) {
            throw new ConfiguratorException(sprintf('Package "%s" not found', $package));
        }
        $content = Yaml::parseFile($source.'/'.self::MANIFEST);

        if (isset($content['require'])) {
            if (!is_array($content['require'])) {
                throw new ConfiguratorException(
                    sprintf('Package "%s" require section must be an array', $package)
                );
            }
            foreach ($content['require'] as $required) {
                $this->createSequence($required, $sequence);
            }
        }
        if (isset($content['sequence']) && is_array($content['sequence'])) {
            $sequence[$package] = [
                'source' => $source,
                'sequence' => $content['sequence'],
            ];
        } else {
            throw new ConfiguratorException(
                sprintf('Package "%s" sequence section is required and must be an array', $package)
            );
        }
    }

    private function getPackageSource(string $package): ?string
    {
        $source = null;
        foreach ($this->repositories as $repository) {
            $path = $repository.'/'.$package;
            if (file_exists($path) && is_dir($path)) {
                $source = $path;
            }
        }

        return $source;
    }
}
