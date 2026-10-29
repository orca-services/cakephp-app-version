<?php
declare(strict_types=1);

namespace AppVersion\Controller;

use AppVersion\AppVersion;
use Cake\Controller\Controller;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;

/**
 * App Version controller
 *
 * Serves the app version as JSON, e.g. `{"version":"1.2.3"}`.
 */
class VersionController extends Controller
{
    /**
     * Return the app version as JSON.
     *
     * @return Response
     * @throws NotFoundException If the endpoint is disabled.
     */
    public function index(): Response
    {
        if (!AppVersion::isEnabled('endpoint')) {
            throw new NotFoundException();
        }

        $this->request->allowMethod(['get']);

        return $this->response
            ->withType('application/json')
            ->withHeader('Cache-Control', 'no-store')
            ->withStringBody((string)json_encode(['version' => AppVersion::get()]));
    }
}
