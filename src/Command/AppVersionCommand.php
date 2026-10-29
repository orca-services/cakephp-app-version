<?php
declare(strict_types=1);

namespace AppVersion\Command;

use AppVersion\AppVersion;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

/**
 * AppVersion command
 *
 * Prints the app version, e.g. for deploy scripts:
 *
 * ```
 * bin/cake app_version
 * bin/cake app_version -f
 * bin/cake app_version --full
 * ```
 */
class AppVersionCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'app_version';
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Print the app version.';
    }

    /**
     * Build the info line with the PHP, CakePHP and app version.
     *
     * E.g. `PHP 8.3.6, CakePHP 5.3.6 (debug: false), app version 1.2.3`
     *
     * @return string
     */
    public static function infoLine(): string
    {
        return sprintf(
            'PHP %s, CakePHP %s (debug: %s), app version %s',
            PHP_VERSION,
            Configure::version(),
            Configure::read('debug') ? 'true' : 'false',
            AppVersion::get(),
        );
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription(static::getDescription())
            ->addOption('full', [
                'short' => 'f',
                'boolean' => true,
                'help' => 'Also print the PHP version, the CakePHP version and the debug mode.',
            ]);

        return $parser;
    }

    /**
     * Print the app version.
     *
     * @param Arguments $args The command arguments.
     * @param ConsoleIo $io The console io
     * @return int The exit code
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        if ($args->getOption('full')) {
            $io->out(static::infoLine());
        } else {
            $io->out(AppVersion::get());
        }

        return static::CODE_SUCCESS;
    }
}
