<?php

namespace Kartavik\Kartigex\Random;

use Kartavik\Kartigex\Exception;

/**
 * Class Factory
 * @package Kartavik\Kartigex\Random
 *
 * @author Lewis Dyer <getintouch@icomefromthenet.com>
 * @author Roman <KartaviK> Varkuta <roman.varkuta@gmail.com>
 */
class Factory
{
    /**
     * Each Generator must implement the Kartigex\RandomInterface
     *
     * @var string[] List of Generators
     */
    protected static $types = [
        'srand' => Srand::class,
        'mersenne' => MersenneRandom::class,
        'simple' => Simple::class,
    ];

    public static function registerExtension(string $name, string $generator): string
    {
        return static::$types[strtolower($name)] = $generator;
    }

    public static function registerExtensions(array $extension): void
    {
        foreach ($extension as $key => $ns) {
            static::registerExtension($key, $ns);
        }
    }

    /**
     * Resolve a Doctrine DataType Class
     *
     * @param string $type
     * @param int|null $seed
     *
     * @return mixed
     * @throws Exception
     */
    public function create(string $type, int $seed = null)
    {
        $type = strtolower($type);

        # check extension list

        if (isset(self::$types[$type]) === true) {
            # assign platform the full namespace
            if (class_exists(self::$types[$type]) === false) {
                throw new Exception('Unknown Generator at::' . $type);
            }

            $type = self::$types[$type];
        } else {
            throw new Exception('Unknown Generator at::' . $type);
        }

        return new $type($seed);
    }
}
