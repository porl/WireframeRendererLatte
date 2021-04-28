<?php

declare(strict_types=1);

namespace ProcessWire;

use Latte\Engine;

/**
 * Wireframe Renderer Blade
 *
 * @version 0.2.0
 * @author Maurizio Bonani <maurizio.bonani@gmail.com>
 * @license Mozilla Public License v2.0 https://mozilla.org/MPL/2.0/
 */
class WireframeRendererLatte extends Wire implements Module
{
    /**
     * The Latte instance.
     *
     * @var Engine
     */
    protected $latte;

    /**
     * Default extension.
     *
     * @var string
     */
    protected $ext = 'latte';

    /**
     * Init method
     *
     * @param array $settings Additional settings.
     * @return WireframeRendererLatte
     */
    public function ___init(array $settings = []): WireframeRendererLatte
    {
        // autoload Latte classes
        if (!class_exists('\Latte\Engine')) {
            require_once(__DIR__ . '/vendor/autoload.php' /*NoCompile*/);
        }

        $this->latte = $this->initLatte($settings);

        return $this;
    }

    /**
     * Init Latte
     *
     * @param array $settings Latte settings.
     * @return Engine
     */
    public function ___initLatte(array $settings = []): Engine
    {

        $cache = $settings['cache'] ?? $this->wire('config')->paths->cache . 'WireframeRendererLatte';

        if (!empty($settings['ext'])) {
            $this->ext = $settings['ext'];
        }

        $latte = new Engine;
        $latte->setTempDirectory($cache);

//        $latte->addNamespace('layout', $viewPaths['layout']);
//        $latte->addNamespace('partial', $viewPaths['partial']);
//        $latte->addNamespace('component', $viewPaths['component']);

        return $latte;
    }

    /**
     * Render method
     *
     * @param string $type Type of file to render (view, layout, partial, or component).
     * @param string $view Name of the view file to render.
     * @param array $context Variables used for rendering.
     * @return string Rendered markup.
     * @throws WireException if param $type has an unexpected value.
     */
    public function render(string $type, string $view, array $context = []): string
    {
        $wireframe = $this->wire('modules')->get('Wireframe');
        $viewPaths = $wireframe->getViewPaths();

        if (! array_key_exists($type, $viewPaths)) {
            throw new WireException(sprintf('Unexpected type (%s).', $type));
        }
        $baseDir = $viewPaths[$type];
        $view = $this->adaptView($view, $baseDir);

        return $this->latte->renderToString($view, $context);
    }

    /**
     * Adapt view path to Latte notation.
     *
     * @param  string $view
     * @return string
     */
    protected function adaptView($view, $baseDir)
    {
        return $baseDir . $view;
    }

    /**
     * @return Engine
     */
    public function getLatteInstance(): Engine
    {
        return $this->latte;
    }

    /**
     * @return string
     */
    public function getExt(): string
    {
        return $this->ext;
    }
}
