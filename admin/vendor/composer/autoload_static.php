<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite9f218fa4428cf3dae8c56eb8583445c
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Midnite81\\Xml2Array\\' => 20,
        ),
        'A' => 
        array (
            'Ahc\\Jwt\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Midnite81\\Xml2Array\\' => 
        array (
            0 => __DIR__ . '/..' . '/midnite81/xml2array/src',
        ),
        'Ahc\\Jwt\\' => 
        array (
            0 => __DIR__ . '/..' . '/adhocore/jwt/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite9f218fa4428cf3dae8c56eb8583445c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite9f218fa4428cf3dae8c56eb8583445c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
