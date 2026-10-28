<?php
declare(strict_types=1);

namespace AppVersion;

use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * AppVersion
 *
 * Central access point for the app version and the plugin configuration.
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
     * Default configuration of the output options.
     *
     * Every output that is reachable from outside the server is disabled by default,
     * because exposing the exact version can help attackers to fingerprint the app.
     *
     * @var array<string, array<string, mixed>>
     */
    public const DEFAULTS = [
        'meta' => [
            'enabled' => false,
            'name' => 'app-version',
        ],
        'endpoint' => [
            'enabled' => false,
            'path' => '/version',
            'middleware' => [],
        ],
        'cli' => [
            'enabled' => true,
            'banner' => false,
        ],
    ];

    /**
     * Get the configured app version.
     *
     * @return string The app version or "Unknown" if it is not set.
     */
    public static function get(): string
    {
        $version = Configure::read('App.version');

        if ($version === null || $version === '' || !is_scalar($version)) {
            return self::UNKNOWN;
        }

        return (string)$version;
    }

    /**
     * Read a plugin configuration value, falling back to the plugin default.
     *
     * @param string $key The key below `AppVersion`, e.g. `endpoint.path`.
     * @return mixed The configured value or the default.
     */
    public static function config(string $key): mixed
    {
        return Configure::read('AppVersion.' . $key, Hash::get(self::DEFAULTS, $key));
    }

    /**
     * Check whether an output option is enabled.
     *
     * @param string $output The output option, one of `meta`, `endpoint` or `cli`.
     * @return bool
     */
    public static function isEnabled(string $output): bool
    {
        return (bool)self::config($output . '.enabled');
    }
}
