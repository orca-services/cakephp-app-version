<?php
declare(strict_types=1);

namespace AppVersion\Test\TestCase\View\Helper;

use AppVersion\View\Helper\AppVersionHelper;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * AppVersionHelper Test Case
 */
class AppVersionHelperTest extends TestCase
{
    /**
     * @var View
     */
    protected View $view;

    /**
     * Test subject
     *
     * @var AppVersionHelper
     */
    protected AppVersionHelper $AppVersion;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.version', '1.2.3');

        $this->view = new View();
        $this->AppVersion = new AppVersionHelper($this->view);
    }

    /**
     * Test get method
     *
     * @return void
     * @uses AppVersionHelper::get
     */
    public function testGet(): void
    {
        self::assertSame('1.2.3', $this->AppVersion->get());

        Configure::delete('App.version');
        self::assertSame('Unknown', $this->AppVersion->get());
    }

    /**
     * Test getUrlEncoded method
     *
     * @return void
     * @uses AppVersionHelper::getUrlEncoded
     */
    public function testGetUrlEncoded(): void
    {
        self::assertSame('1.2.3', $this->AppVersion->getUrlEncoded());

        Configure::write('App.version', '1.2.3+build 42/a&b');
        self::assertSame('1.2.3%2Bbuild%2042%2Fa%26b', $this->AppVersion->getUrlEncoded());

        Configure::delete('App.version');
        self::assertSame('Unknown', $this->AppVersion->getUrlEncoded());
    }

    /**
     * Test meta method
     *
     * @return void
     * @uses AppVersionHelper::meta
     */
    public function testMeta(): void
    {
        self::assertSame('<meta name="app-version" content="1.2.3">', $this->AppVersion->meta());
    }

    /**
     * Test meta method escapes the version
     *
     * @return void
     * @uses AppVersionHelper::meta
     */
    public function testMetaEscaping(): void
    {
        Configure::write('App.version', '1.2.3"><script>');

        self::assertSame(
            '<meta name="app-version" content="1.2.3&quot;&gt;&lt;script&gt;">',
            $this->AppVersion->meta(),
        );
    }

    /**
     * Test meta method appending to a view block
     *
     * @return void
     * @uses AppVersionHelper::meta
     */
    public function testMetaBlock(): void
    {
        self::assertNull($this->AppVersion->meta(['block' => true]));
        self::assertSame('<meta name="app-version" content="1.2.3">', $this->view->fetch('meta'));
    }
}
