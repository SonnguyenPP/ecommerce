<?php

namespace App\Lib\Themeable;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Container\Container;
use App\Lib\Themeable\ThemeContract;
use Illuminate\View\ViewFinderInterface;

class Theme implements ThemeContract
{
    /**
     * Collection of all theme information.
     *
     * @var array
     */
    protected $themes = [];

    /**
     * Blade View Finder instance.
     *
     * @var \Illuminate\View\ViewFinderInterface
     */
    protected $finder;

    /**
     * Application container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $app;

    /**
     * Config repository instance.
     *
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * Current active theme.
     *
     * @var string|null
     */
    private $activeTheme = null;

    /**
     * Theme constructor.
     *
     * @param \Illuminate\Container\Container $app
     * @param \Illuminate\View\ViewFinderInterface $finder
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct(Container $app, ViewFinderInterface $finder, Repository $config)
    {
        $this->app = $app;
        $this->finder = $finder;
        $this->config = $config;
        $this->activeTheme = $this->config->get('theme.active');
    }

    /**
     * Add a new theme.
     *
     * @param string $theme
     * @param array $themeInfo
     */
    public function add(string $theme, array $themeInfo = []): void
    {
        $this->themes[$theme] = $themeInfo;
    }

    /**
     * Set the current theme.
     *
     * @param string $theme
     *
     * @throws \InvalidArgumentException
     */
    public function set(string $theme): void
    {
        if (!$this->has($theme)) {
            return;
        }

        $this->loadTheme($theme);
        $this->activeTheme = $theme;
    }

    /**
     * Check if a theme exists.
     *
     * @param string $theme
     *
     * @return bool
     */
    public function has(string $theme): bool
    {
        return array_key_exists($theme, $this->themes);
    }

    /**
     * Get information about a particular theme.
     *
     * @param string $themeName
     *
     * @return \Illuminate\Support\Collection
     */
    public function getThemeInfo(string $themeName): Collection
    {
        return isset($this->themes[$themeName]) ? collect($this->themes[$themeName])->prepend($themeName, 'name') : collect();
    }

    /**
     * Get the current theme or information about a specific theme.
     *
     * @param string|null $theme
     *
     * @return \Illuminate\Support\Collection
     */
    public function get(string $theme = null): Collection
    {
        return is_null($theme) ? $this->getThemeInfo($this->activeTheme) : $this->getThemeInfo($theme);
    }

    /**
     * Get information about the current active theme.
     *
     * @return \Illuminate\Support\Collection
     */
    public function current(): Collection
    {
        return $this->getThemeInfo($this->activeTheme);
    }

    /**
     * Get information about all themes.
     *
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return collect($this->themes);
    }

    /**
     * Load the specified theme by mapping its view path.
     *
     * @param string $theme
     */
    private function loadTheme(string $theme): void
    {
        if (!$this->has($theme)) {
            return;
        }
        
        $themeInfo = $this->getThemeInfo($theme);

        if (!$themeInfo->has('view_path')) {
            return;
        }

        $viewPath = $themeInfo->get('view_path');
        $this->finder->prependLocation($viewPath);
        $this->finder->prependNamespace($themeInfo->get('name'), $viewPath);
    }
}
