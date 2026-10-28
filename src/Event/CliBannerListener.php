<?php
declare(strict_types=1);

namespace AppVersion\Event;

use AppVersion\AppVersion;
use AppVersion\Command\AppVersionCommand;
use Cake\Console\ConsoleIo;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;

/**
 * CLI banner listener
 *
 * Prints the PHP version, the CakePHP version, the debug mode and the app version
 * as the first line of every CLI command, e.g.:
 *
 * ```
 * PHP 8.3.6, CakePHP 5.3.6 (debug: false), app version 1.2.3
 * ```
 *
 * The line is written to stderr, so the regular output of commands
 * can still be piped or parsed. It is suppressed with `--quiet`.
 */
class CliBannerListener implements EventListenerInterface
{
    /**
     * @inheritDoc
     */
    public function implementedEvents(): array
    {
        return [
            'Command.beforeExecute' => 'printBanner',
        ];
    }

    /**
     * Print the banner before a command is executed.
     *
     * @param EventInterface $event The Command.beforeExecute event.
     * @return void
     */
    public function printBanner(EventInterface $event): void
    {
        if (!AppVersion::isEnabled('cli') || !AppVersion::config('cli.banner')) {
            return;
        }

        if ($event->getSubject() instanceof AppVersionCommand) {
            return;
        }

        $io = $event->getData('io');
        if (!$io instanceof ConsoleIo || $io->level() === ConsoleIo::QUIET) {
            return;
        }

        $io->err(AppVersionCommand::infoLine());
    }
}
