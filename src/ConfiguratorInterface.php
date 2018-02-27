<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\ServiceConfigurator;

interface ConfiguratorInterface
{
    /**
     * Get configurator source.
     */
    public function getSource(): ?string;

    /**
     * Get configurator target.
     */
    public function getTarget(): ?string;

    /**
     * Apply configuration.
     */
    public function apply(): void;
}
