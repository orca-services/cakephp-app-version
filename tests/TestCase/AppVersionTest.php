<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase;

use AppVersion\AppVersion;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * AppVersion Test Case
 */
class AppVersionTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::delete('App.version');
        Configure::delete('AppVersion');
    }

    /**
     * Test get method
     *
     * @return void
     * @uses AppVersion::get
     */
    public function testGet(): void
    {
        Configure::write('App.version', '1.2.3');

        self::assertSame('1.2.3', AppVersion::get());
    }

    /**
     * Test get method when the app version is not set or empty
     *
     * @return void
     * @uses AppVersion::get
     */
    public function testGetNotSet(): void
    {
        self::assertSame('Unknown', AppVersion::get());

        Configure::write('App.version', '');
        self::assertSame('Unknown', AppVersion::get());
    }

    /**
     * Test config method returns the defaults
     *
     * @return void
     * @uses AppVersion::config
     */
    public function testConfigDefaults(): void
    {
        self::assertFalse(AppVersion::config('meta.enabled'));
        self::assertSame('app-version', AppVersion::config('meta.name'));
        self::assertFalse(AppVersion::config('endpoint.enabled'));
        self::assertSame('/version', AppVersion::config('endpoint.path'));
        self::assertSame([], AppVersion::config('endpoint.middleware'));
        self::assertTrue(AppVersion::config('cli.enabled'));
        self::assertNull(AppVersion::config('does.not.exist'));
    }

    /**
     * Test config method returns configured values
     *
     * @return void
     * @uses AppVersion::config
     * @uses AppVersion::isEnabled
     */
    public function testConfigConfigured(): void
    {
        Configure::write('AppVersion.endpoint', ['enabled' => true, 'path' => '/status/version']);

        self::assertTrue(AppVersion::isEnabled('endpoint'));
        self::assertSame('/status/version', AppVersion::config('endpoint.path'));
        self::assertFalse(AppVersion::isEnabled('meta'));
    }
}
