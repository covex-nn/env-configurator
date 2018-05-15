<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Environment\Configurator;

use Symfony\Component\Yaml\Yaml;

/**
 * Replace with str_replace.
 */
class ReplaceConfigurator implements ConfiguratorInterface
{
    public function apply(string $source, string $target): void
    {
        $search = [];
        $replace = [];
        $data = Yaml::parseFile($source);
        if (!is_array($data)) {
            throw new ConfiguratorException(sprintf('Source must be an array in %s', $source));
        }
        foreach ($data as $key => $value) {
            if (!is_scalar($value)) {
                throw new ConfiguratorException(
                    sprintf("Replace value for '%s' key must be scalar in %s", $key, $source)
                );
            }
            $search[] = $key;
            $replace[] = $value;
        }

        if (!file_exists($target)) {
            throw new ConfiguratorException(sprintf('Target file %s not found', $target));
        }

        file_put_contents($target, str_replace($search, $replace, file_get_contents($target)));
    }
}
