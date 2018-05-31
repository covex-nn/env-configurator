<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Composer;

use Composer\Composer;
use Composer\Package\CompletePackage;
use Composer\Package\PackageInterface;
use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;
use Covex\Environment\Configurator\ComposerConfigurator;
use Covex\Environment\Configurator\CopyConfigurator;
use Covex\Environment\Configurator\DiffConfigurator;
use Covex\Environment\Configurator\DockerComposeConfigurator;
use Covex\Environment\Configurator\ReplaceConfigurator;
use Covex\Environment\Configurator\YamlConfigurator;

class CommandProvider implements CommandProviderCapability
{
    private const REPOSITORY_PROVIDER = 'environment-repository';

    /**
     * @var Composer
     */
    private $composer;

    public function __construct(array $data)
    {
        $this->composer = $data['composer'];
    }

    public function getCommands(): array
    {
        $apply = new ApplyCommand();
        $apply
            ->addConfigurator('copy', new CopyConfigurator())
            ->addConfigurator('diff', new DiffConfigurator())
            ->addConfigurator('docker-compose', new DockerComposeConfigurator())
            ->addConfigurator('replace', new ReplaceConfigurator())
            ->addConfigurator('yaml', new YamlConfigurator())
            ->addConfigurator('composer', new ComposerConfigurator())
        ;
        $packages = $this->composer->getRepositoryManager()->getLocalRepository()->getPackages();
        /** @var PackageInterface[] $packages */
        foreach ($packages as $package) {
            if (!$package instanceof CompletePackage) {
                continue;
            }
            $path = $package->getExtra()[self::REPOSITORY_PROVIDER] ?? null;
            if (null !== $path) {
                $apply->addRepository(
                    $this->composer->getInstallationManager()->getInstallPath($package).'/'.$path
                );
            }
        }

        return [$apply];
    }
}
