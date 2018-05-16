<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

interface ConfiguratorInterface
{
    /**
     * Apply configuration.
     */
    public function apply(string $source, string $target): void;
}