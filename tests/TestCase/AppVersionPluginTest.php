<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase;

use AppVersion\AppVersionPlugin;
use AppVersion\Command\AppVersionCommand;
use Cake\Console\CommandCollection;
use Cake\TestSuite\TestCase;

/**
 * AppVersionPlugin Test Case
 */
class AppVersionPluginTest extends TestCase
{
    /**
     * Test the plugin does not connect routes on its own
     *
     * @return void
     */
    public function testRoutesDisabled(): void
    {
        self::assertFalse((new AppVersionPlugin())->isEnabled('routes'));
    }

    /**
     * Test the command is added
     *
     * @return void
     * @uses AppVersionPlugin::console
     */
    public function testConsole(): void
    {
        $commands = (new AppVersionPlugin())->console(new CommandCollection());

        self::assertTrue($commands->has('app_version'));
        self::assertSame(AppVersionCommand::class, $commands->get('app_version'));
    }
}
