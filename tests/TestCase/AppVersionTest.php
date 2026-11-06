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
     * Test get method when the app version is not set, empty or not scalar
     *
     * @return void
     * @uses AppVersion::get
     */
    public function testGetNotSet(): void
    {
        self::assertSame('Unknown', AppVersion::get());

        Configure::write('App.version', '');
        self::assertSame('Unknown', AppVersion::get());

        Configure::write('App.version', ['1.2.3']);
        self::assertSame('Unknown', AppVersion::get());
    }
}
