<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase;

use AppVersion\AppVersion;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * AppVersion Test Case
 *
 * @coversDefaultClass \AppVersion\AppVersion
 */
class AppVersionTest extends TestCase
{
    /**
     * Virtual file system root for the version files
     *
     * @var vfsStreamDirectory
     */
    protected vfsStreamDirectory $root;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::delete('App.version');
        $this->root = vfsStream::setup();
    }

    /**
     * Test get method
     *
     * @return void
     * @covers ::get
     */
    public function testGet(): void
    {
        Configure::write('App.version', '1.2.3');

        self::assertSame('1.2.3', AppVersion::get());
    }

    /**
     * Test get method when the app version is not set, empty or not scalar
     *
     * @return void
     * @covers ::get
     */
    public function testGetNotSet(): void
    {
        self::assertSame('Unknown', AppVersion::get());

        Configure::write('App.version', '');
        self::assertSame('Unknown', AppVersion::get());

        Configure::write('App.version', ['1.2.3']);
        self::assertSame('Unknown', AppVersion::get());
    }

    /**
     * Test extractVersion method trims the version from the version file
     *
     * @return void
     * @covers ::extractVersion
     */
    public function testExtractVersion(): void
    {
        $versionFile = $this->createVersionFile(" 1.2.3\n");

        self::assertSame('1.2.3', AppVersion::extractVersion($versionFile));
    }

    /**
     * Test extractVersion method when the version file is missing or empty
     *
     * @return void
     * @covers ::extractVersion
     */
    public function testExtractVersionMissingOrEmpty(): void
    {
        self::assertSame('Unknown', AppVersion::extractVersion(vfsStream::url('root/missing-version-file')));
        self::assertSame('Unknown', AppVersion::extractVersion(vfsStream::url('root')));
        self::assertSame('Unknown', AppVersion::extractVersion($this->createVersionFile(" \n")));
    }

    /**
     * Test extractVersion method reads `ROOT/VERSION` by default
     *
     * @return void
     * @covers ::extractVersion
     */
    public function testExtractVersionDefaultFile(): void
    {
        self::assertSame(ROOT . DS . 'VERSION', AppVersion::DEFAULT_VERSION_FILE);

        $expected = is_file(AppVersion::DEFAULT_VERSION_FILE)
            ? AppVersion::extractVersion(AppVersion::DEFAULT_VERSION_FILE)
            : 'Unknown';
        self::assertSame($expected, AppVersion::extractVersion());
    }

    /**
     * Create a version file in the virtual file system
     *
     * @param string $content The content of the version file.
     * @return string The URL of the version file.
     */
    protected function createVersionFile(string $content): string
    {
        return vfsStream::newFile('VERSION')
            ->withContent($content)
            ->at($this->root)
            ->url();
    }
}
