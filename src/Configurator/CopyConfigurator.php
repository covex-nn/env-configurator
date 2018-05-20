<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

use Covex\Environment\Util\DirectoryUtil;

class CopyConfigurator implements ConfiguratorInterface
{
    public function apply(string $source, string $target): void
    {
        if (file_exists($target)) {
            throw new ConfiguratorException(sprintf('File %s already exists', $target));
        }

        DirectoryUtil::createParent($target);
        copy($source, $target);
    }
}
