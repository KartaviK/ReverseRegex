<?php

namespace Kartavik\Kartigex\Random;

/**
 * Class Factory
 * @package Kartavik\Kartigex\Random
 *
 * Generator Factory
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
    protected static $types = array(
        'srand' => '\\ReverseRegex\\Random\\SrandRandom',
        'mersenne' => '\\ReverseRegex\\Random\\MersenneRandom',
        'simple' => '\\ReverseRegex\\Random\\Simple',
    );

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
     * Resolve a Dcotrine DataType Class
     *
     * @param string the random generator type name
     *
     */
    public function create($type, $seed = null)
    {
        $type = strtolower($type);

        # check extension list

        if (isset(self::$types[$type]) === true) {
            # assign platform the full namespace
            if (class_exists(self::$types[$type]) === false) {
                throw new ReverseRegexException('Unknown Generator at::' . $type);
            }

            $type = self::$types[$type];
        } else {
            throw new ReverseRegexException('Unknown Generator at::' . $type);
        }

        return new $type($seed);
    }
}
