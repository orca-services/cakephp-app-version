<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\Command;

use AppVersion\Command\AppVersionCommand;
use AppVersion\Test\TestApp\TestApplication;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * AppVersionCommand Test Case
 *
 * @coversDefaultClass \AppVersion\Command\AppVersionCommand
 */
class AppVersionCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->configApplication(TestApplication::class, [CONFIG]);
        Configure::write('App.version', '1.2.3');
    }

    /**
     * Test the command name
     *
     * @return void
     * @covers ::defaultName
     */
    public function testDefaultName(): void
    {
        self::assertSame('app_version', AppVersionCommand::defaultName());
    }

    /**
     * Test the help output with the description and the full option
     *
     * @return void
     * @covers ::getDescription
     * @covers ::buildOptionParser
     */
    public function testHelp(): void
    {
        $this->exec('app_version --help');

        $this->assertExitSuccess();
        $this->assertOutputContains('Print the app version.');
        $this->assertOutputContains('--full, -f');
        $this->assertOutputContains('Also print the PHP version');
    }

    /**
     * Test execute method
     *
     * @return void
     * @covers ::execute
     */
    public function testExecute(): void
    {
        $this->exec('app_version');

        $this->assertExitSuccess();
        $this->assertOutputContains('1.2.3');
    }

    /**
     * Test execute method when the app version is not set
     *
     * @return void
     * @covers ::execute
     */
    public function testExecuteAppVersionNotSet(): void
    {
        Configure::delete('App.version');

        $this->exec('app_version');

        $this->assertExitSuccess();
        $this->assertOutputContains('Unknown');
    }

    /**
     * Test execute method with the full option
     *
     * @return void
     * @covers ::execute
     * @covers ::infoLine
     */
    public function testExecuteFull(): void
    {
        Configure::write('debug', false);

        $this->exec('app_version --full');

        $this->assertExitSuccess();
        $this->assertOutputContains(sprintf(
            'PHP %s, CakePHP %s (debug: false), app version 1.2.3',
            PHP_VERSION,
            Configure::version(),
        ));
    }

    /**
     * Test execute method with the short full option
     *
     * @return void
     * @covers ::execute
     * @covers ::infoLine
     */
    public function testExecuteFullShort(): void
    {
        $this->exec('app_version -f');

        $this->assertExitSuccess();
        $this->assertOutputContains(sprintf(
            'PHP %s, CakePHP %s (debug: true), app version 1.2.3',
            PHP_VERSION,
            Configure::version(),
        ));
    }
}
