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
use Composer\Composer;
use Composer\Package\CompletePackage;
use Composer\Package\PackageInterface;
use Covex\Environment\Configurator\CommandAwareInterface;
use Covex\Environment\Configurator\ConfiguratorException;
use Covex\Environment\Configurator\ConfiguratorInterface;
use Covex\Stream\FileSystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class ApplyCommand extends BaseCommand
{
    protected const MANIFEST = 'manifest.yaml';

    protected const REPOSITORY_PROVIDER = 'environment-repository';

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

        if ($configurator instanceof CommandAwareInterface) {
            $configurator->setCommand($this);
        }

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

    /**
     * @return $this
     */
    public function importRepositories(?Composer $composer): self
    {
        if (null !== $composer) {
            $packages = $composer->getRepositoryManager()->getLocalRepository()->getPackages();
            /** @var PackageInterface[] $packages */
            foreach ($packages as $package) {
                if (!$package instanceof CompletePackage) {
                    continue;
                }
                $path = $package->getExtra()[self::REPOSITORY_PROVIDER] ?? null;
                if (null !== $path) {
                    $this->addRepository(
                        $composer->getInstallationManager()->getInstallPath($package).'/'.$path
                    );
                }
            }
        }

        return $this;
    }

    protected function configure(): void
    {
        $this
            ->setName('env:apply')
            ->setDescription('Apply configuration package')
            ->addArgument('sequence', InputArgument::REQUIRED, 'Sequence name')
            ->addArgument('target', InputArgument::OPTIONAL, 'Target directory', getcwd());
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $package = $input->getArgument('sequence');
        $target = $input->getArgument('target');

        $sequence = [];
        $this->createSequence($package, $sequence);

        FileSystem::register('target', $target);
        foreach ($sequence as $item) {
            foreach ($item['sequence'] as $message => $configurators) {
                $output->writeln(sprintf('Applying "%s" configuration sequence:', $message));
                foreach ($configurators as $name => $files) {
                    $configurator = $this->getConfigurator($name);
                    foreach ($files as $source => $target) {
                        try {
                            $configurator->apply($item['source'].'/'.$source, 'target://'.$target);
                        } catch (ConfiguratorException $e) {
                            FileSystem::unregister('target');

                            throw new ConfiguratorException(
                                str_replace('target://', '', $e->getMessage()), $e->getCode()
                            );
                        }
                        if ($output->isVerbose()) {
                            $output->writeln(sprintf('    [%s] %s', $name, $target));
                        }
                    }
                }
                $output->writeln('');
            }
            FileSystem::commit('target');
        }
        FileSystem::unregister('target');
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
