<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitd1a8cb0988bfb55193accdde8e2f80ee
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitd1a8cb0988bfb55193accdde8e2f80ee', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitd1a8cb0988bfb55193accdde8e2f80ee', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitd1a8cb0988bfb55193accdde8e2f80ee::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}