<?php
declare(strict_types=1);

namespace AppVersion\View\Helper;

use AppVersion\AppVersion;
use Cake\View\Helper;

/**
 * AppVersion helper
 *
 * Makes the app version available in templates, e.g. for a footer or an HTML meta tag.
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class AppVersionHelper extends Helper
{
    /**
     * The `name` attribute of the meta tag.
     *
     * @var string
     */
    public const META_NAME = 'app-version';

    /**
     * Helpers used by this helper
     *
     * @var array
     */
    protected array $helpers = ['Html'];

    /**
     * Get the app version.
     *
     * The value is not escaped, use `h()` when you print it.
     *
     * @return string The app version or "Unknown" if it is not set.
     */
    public function get(): string
    {
        return AppVersion::get();
    }

    /**
     * Get the URL encoded app version.
     *
     * @return string The URL encoded app version or "Unknown" if it is not set.
     */
    public function getUrlEncoded(): string
    {
        return rawurlencode($this->get());
    }

    /**
     * Get an HTML meta tag containing the app version.
     *
     * @param array<string, mixed> $options Options passed to `HtmlHelper::meta()`
     * @return string|null The meta tag or null if it was appended to a block.
     */
    public function meta(array $options = []): ?string
    {
        return $this->Html->meta(['name' => static::META_NAME, 'content' => $this->get()], null, $options);
    }
}
