<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8e190cd24c7f772712c56e28d8c09bf9
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8e190cd24c7f772712c56e28d8c09bf9::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8e190cd24c7f772712c56e28d8c09bf9::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}