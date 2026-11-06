<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase;

use AppVersion\AppVersionPlugin;
use AppVersion\Command\AppVersionCommand;
use Cake\Console\CommandCollection;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * AppVersionPlugin Test Case
 */
class AppVersionPluginTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::delete('AppVersion');
    }

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
     * Test the command is added by default
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

    /**
     * Test the command is not added if the CLI output is disabled
     *
     * @return void
     * @uses AppVersionPlugin::console
     */
    public function testConsoleDisabled(): void
    {
        Configure::write('AppVersion.cli.enabled', false);

        $commands = (new AppVersionPlugin())->console(new CommandCollection());

        self::assertFalse($commands->has('app_version'));
    }
}
