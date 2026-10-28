<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\Event;

use AppVersion\Command\AppVersionCommand;
use AppVersion\Event\CliBannerListener;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\StubConsoleOutput;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\TestSuite\TestCase;

/**
 * CliBannerListener Test Case
 */
class CliBannerListenerTest extends TestCase
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
        Configure::write('debug', true);
        Configure::delete('AppVersion');

        $this->out = new StubConsoleOutput();
        $this->err = new StubConsoleOutput();
        $this->io = new ConsoleIo($this->out, $this->err);
    }

    /**
     * Dispatch a Command.beforeExecute event to the listener
     *
     * @param object $subject The command.
     * @return void
     */
    protected function dispatch(object $subject): void
    {
        $event = new Event('Command.beforeExecute', $subject, [
            'args' => new Arguments([], [], []),
            'io' => $this->io,
        ]);

        (new CliBannerListener())->printBanner($event);
    }

    /**
     * Test the implemented events
     *
     * @return void
     * @uses CliBannerListener::implementedEvents
     */
    public function testImplementedEvents(): void
    {
        self::assertSame(
            ['Command.beforeExecute' => 'printBanner'],
            (new CliBannerListener())->implementedEvents(),
        );
    }

    /**
     * Test the banner is disabled by default
     *
     * @return void
     * @uses CliBannerListener::printBanner
     */
    public function testPrintBannerDisabledByDefault(): void
    {
        $this->dispatch(new Command());

        self::assertSame([], $this->out->messages());
        self::assertSame([], $this->err->messages());
    }

    /**
     * Test the banner is printed to stderr when enabled
     *
     * @return void
     * @uses CliBannerListener::printBanner
     */
    public function testPrintBanner(): void
    {
        Configure::write('AppVersion.cli.banner', true);

        $this->dispatch(new Command());

        $expected = sprintf(
            'PHP %s, CakePHP %s (debug: true), app version 1.2.3',
            PHP_VERSION,
            Configure::version(),
        );
        self::assertSame([], $this->out->messages());
        self::assertSame([$expected], $this->err->messages());
    }

    /**
     * Test the banner is not printed in quiet mode
     *
     * @return void
     * @uses CliBannerListener::printBanner
     */
    public function testPrintBannerQuiet(): void
    {
        Configure::write('AppVersion.cli.banner', true);
        $this->io->level(ConsoleIo::QUIET);

        $this->dispatch(new Command());

        self::assertSame([], $this->err->messages());
    }

    /**
     * Test the banner is not printed if the CLI output is disabled
     *
     * @return void
     * @uses CliBannerListener::printBanner
     */
    public function testPrintBannerCliDisabled(): void
    {
        Configure::write('AppVersion.cli', ['enabled' => false, 'banner' => true]);

        $this->dispatch(new Command());

        self::assertSame([], $this->err->messages());
    }

    /**
     * Test the banner is not printed for the app_version command itself
     *
     * @return void
     * @uses CliBannerListener::printBanner
     */
    public function testPrintBannerSkipsAppVersionCommand(): void
    {
        Configure::write('AppVersion.cli.banner', true);

        $this->dispatch(new AppVersionCommand());

        self::assertSame([], $this->err->messages());
    }
}
