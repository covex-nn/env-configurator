<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

class CopyConfigurator implements ConfiguratorInterface
{
    public function apply(string $source, string $target): void
    {
        if (file_exists($target)) {
            throw new ConfiguratorException(sprintf('File %s already exists', $target));
        }
        $info = parse_url($target);
        if (!isset($info['host'])) {
            $dir = pathinfo($target, PATHINFO_DIRNAME);
        } else {
            $dir = $info['scheme'].'://';
            if (isset($info['path'])) {
                $dir .= dirname($info['host'].$info['path']);
            }
        }

        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new ConfiguratorException(sprintf('%s is not a directory', $dir));
            }
        } else {
            mkdir($dir, 0777, true);
        }
        copy($source, $target);
    }
}
