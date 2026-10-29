<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\Command;

use AppVersion\Command\AppVersionCommand;
use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\StubConsoleOutput;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * AppVersionCommand Test Case
 */
class AppVersionCommandTest extends TestCase
{
    /**
     * @var StubConsoleOutput
     */
    protected StubConsoleOutput $out;

    /**
     * @var StubConsoleOutput
     */
    protected StubConsoleOutput $err;

    /**
     * @var ConsoleIo
     */
    protected ConsoleIo $io;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.version', '1.2.3');
        Configure::delete('AppVersion');

        $this->out = new StubConsoleOutput();
        $this->err = new StubConsoleOutput();
        $this->io = new ConsoleIo($this->out, $this->err);
    }

    /**
     * Test the command name
     *
     * @return void
     * @uses AppVersionCommand::defaultName
     */
    public function testDefaultName(): void
    {
        self::assertSame('app_version', AppVersionCommand::defaultName());
    }

    /**
     * Test execute method
     *
     * @return void
     * @uses AppVersionCommand::execute
     */
    public function testExecute(): void
    {
        $result = (new AppVersionCommand())->run([], $this->io);

        self::assertSame(AppVersionCommand::CODE_SUCCESS, $result);
        self::assertSame(['1.2.3'], $this->out->messages());
    }

    /**
     * Test execute method when the app version is not set
     *
     * @return void
     * @uses AppVersionCommand::execute
     */
    public function testExecuteAppVersionNotSet(): void
    {
        Configure::delete('App.version');

        (new AppVersionCommand())->run([], $this->io);

        self::assertSame(['Unknown'], $this->out->messages());
    }

    /**
     * Test execute method with the full option
     *
     * @return void
     * @uses AppVersionCommand::execute
     */
    public function testExecuteFull(): void
    {
        Configure::write('debug', false);

        (new AppVersionCommand())->run(['--full'], $this->io);

        $expected = sprintf(
            'PHP %s, CakePHP %s (debug: false), app version 1.2.3',
            PHP_VERSION,
            Configure::version(),
        );
        self::assertSame([$expected], $this->out->messages());
    }
}
