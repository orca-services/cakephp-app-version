<?php
declare(strict_types=1);

namespace AppVersion\Controller;

use AppVersion\AppVersion;
use Cake\Controller\Controller;
use Cake\View\JsonView;

/**
 * App Version controller
 *
 * Serves the app version depending on the requested file extension.
 * This controller is only reachable if the application connects its route.
 */
class VersionController extends Controller
{
    /**
     * View classes by file extension. Requests without a known extension are rendered as HTML.
     *
     * @var array<string, class-string<\Cake\View\View>>
     */
    protected const VIEW_CLASSES = [
        'json' => JsonView::class,
    ];

    /**
     * Show the app version.
     *
     * @return void
     */
    public function index(): void
    {
        $this->request->allowMethod(['get']);

        $this->response = $this->response->withHeader('Cache-Control', 'no-store');
        $this->set('appVersion', AppVersion::get());

        $extension = (string)$this->request->getParam('_ext');
        if (isset(static::VIEW_CLASSES[$extension])) {
            $this->viewBuilder()
                ->setClassName(static::VIEW_CLASSES[$extension])
                ->setOption('serialize', ['app-version' => 'appVersion']);

            return;
        }

        $this->viewBuilder()->disableAutoLayout();
    }
}
