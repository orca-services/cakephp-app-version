<?php
declare(strict_types=1);

namespace AppVersion\Controller;

use AppVersion\AppVersion;
use Cake\Controller\Controller;
use Cake\View\JsonView;
use Cake\View\XmlView;

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
        'xml' => XmlView::class,
    ];

    /**
     * The key of the app version in serialized responses, e.g. `{"app-version":"1.2.3"}`.
     *
     * @var string
     */
    public const SERIALIZE_KEY = 'app-version';

    /**
     * Show the app version.
     *
     * @return void
     */
    public function show(): void
    {
        $request = $this->request;
        $viewBuilder = $this->viewBuilder();
        $request->allowMethod(['get']);

        $this->response = $this->response->withHeader('Cache-Control', 'no-store');
        $appVersion = AppVersion::get();

        $extension = (string)$request->getParam('_ext');
        if (isset(static::VIEW_CLASSES[$extension])) {
            $this->set(static::SERIALIZE_KEY, $appVersion);
            $viewBuilder
                ->setClassName(static::VIEW_CLASSES[$extension])
                ->setOption('serialize', [static::SERIALIZE_KEY]);

            return;
        }

        $this->set(compact('appVersion'));
        $viewBuilder->disableAutoLayout();
    }
}
