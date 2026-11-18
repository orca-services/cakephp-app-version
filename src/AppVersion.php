<?php
declare(strict_types=1);

namespace AppVersion;

use Cake\Core\Configure;

/**
 * App Version
 *
 * Central access point for the app version.
 */
class AppVersion
{
    /**
     * The version string to be returned if `App.version` is not set.
     *
     * @var string
     */
    public const UNKNOWN = 'Unknown';

    /**
     * The default path of the file containing the app version.
     *
     * @var string
     */
    public const DEFAULT_VERSION_FILE = ROOT . DS . 'VERSION';

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

    /**
     * Extract the app version from a version file, e.g. to set `App.version` in `config/bootstrap.php`.
     *
     * Leading and trailing whitespace is removed.
     *
     * @param string $versionFilePath The path of the file containing the app version.
     * @return string The app version or "Unknown" if the file is missing, unreadable or empty.
     */
    public static function extractVersion(string $versionFilePath = self::DEFAULT_VERSION_FILE): string
    {
        if (!is_file($versionFilePath) || !is_readable($versionFilePath)) {
            return self::UNKNOWN;
        }

        $version = trim((string)file_get_contents($versionFilePath));
        if ($version === '') {
            return self::UNKNOWN;
        }

        return $version;
    }
}
