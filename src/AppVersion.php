<?php
declare(strict_types=1);

namespace AppVersion;

use Cake\Core\Configure;

/**
 * AppVersion
 *
 * Central access point for the app version.
 */
class AppVersion
{
    /**
     * The version returned if `App.version` is not set.
     *
     * @var string
     */
    public const UNKNOWN = 'Unknown';

    /**
     * Get the configured app version.
     *
     * @return string The app version or "Unknown" if it is not set.
     */
    public static function get(): string
    {
        $version = Configure::read('App.version');

        if (!$version || !is_scalar($version)) {
            return self::UNKNOWN;
        }

        return (string)$version;
    }
}
