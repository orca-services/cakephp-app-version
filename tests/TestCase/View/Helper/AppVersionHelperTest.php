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
        Configure::delete('AppVersion');

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
     * Test meta method is disabled by default
     *
     * @return void
     * @uses AppVersionHelper::meta
     */
    public function testMetaDisabledByDefault(): void
    {
        self::assertSame('', $this->AppVersion->meta());
    }

    /**
     * Test meta method when enabled
     *
     * @return void
     * @uses AppVersionHelper::meta
     */
    public function testMeta(): void
    {
        Configure::write('AppVersion.meta.enabled', true);

        self::assertSame('<meta name="app-version" content="1.2.3">', $this->AppVersion->meta());
    }

    /**
     * Test meta method with a custom name and escaping
     *
     * @return void
     * @uses AppVersionHelper::meta
     */
    public function testMetaCustomNameAndEscaping(): void
    {
        Configure::write('AppVersion.meta', ['enabled' => true, 'name' => 'acme-version']);
        Configure::write('App.version', '1.2.3"><script>');

        self::assertSame(
            '<meta name="acme-version" content="1.2.3&quot;&gt;&lt;script&gt;">',
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
        Configure::write('AppVersion.meta.enabled', true);

        self::assertNull($this->AppVersion->meta(['block' => true]));
        self::assertSame('<meta name="app-version" content="1.2.3">', $this->view->fetch('meta'));
    }
}
