<?php

namespace App\Services\Paginator;

use Pagerfanta\View\Template\Template;

class BootstrapTemplate extends Template
{
    /**
     * @var string[]
     */
    protected static $defaultOptions = [
        'prev_message'        => '&larr; Previous',
        'next_message'        => 'Next &rarr;',
        'dots_message'        => '&bull;',
        'active_suffix'       => '',
        'css_container_class' => 'pagination',
        'css_prev_class'      => 'prev',
        'css_next_class'      => 'next',
        'css_disabled_class'  => 'disabled',
        'css_dots_class'      => 'disabled',
        'css_active_class'    => 'active',
        'rel_previous'        => 'prev',
        'rel_next'            => 'next',
    ];

    public function container(): string
    {
        return sprintf('<ul class="%s">%%pages%%</ul>',
            $this->option('css_container_class')
        );
    }

    public function previousDisabled(): string
    {
        return $this->spanLi($this->previousDisabledClass(), $this->option('prev_message'));
    }

    /**
     * @param $class
     * @param $text
     * @return string
     */
    protected function spanLi($class, $text): string
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';

        return sprintf('<li><a %s>%s</a></li>', $liClass, $text);
    }

    private function previousDisabledClass(): string
    {
        return $this->option('css_prev_class') . ' ' . $this->option('css_disabled_class');
    }

    /**
     * @param int $page
     * @return string
     */
    public function previousEnabled($page): string
    {
        return $this->pageWithTextAndClass($page, $this->option('prev_message'), $this->option('css_prev_class'), $this->option('rel_previous'));
    }

    /**
     * @param $page
     * @param $text
     * @param $class
     * @param string|null $rel
     * @return string
     */
    private function pageWithTextAndClass($page, $text, $class, ?string $rel = null): string
    {
        return $this->linkLi($class, $this->generateRoute($page), $text, $rel);
    }

    /**
     * @param $class
     * @param $href
     * @param $text
     * @param null $rel
     * @return string
     */
    protected function linkLi($class, $href, $text, $rel = null): string
    {
        $liClass = $class ? sprintf(' class="%s"', $class) : '';
        $rel     = $rel ? sprintf(' rel="%s"', $rel) : 'rel="nofollow"';

        return sprintf('<li><a %s href="%s"%s>%s</a></li>', $liClass, $href, $rel, $text);
    }

    public function nextDisabled()
    {
        return $this->spanLi($this->nextDisabledClass(), $this->option('next_message'));
    }

    private function nextDisabledClass(): string
    {
        return $this->option('css_next_class') . ' ' . $this->option('css_disabled_class');
    }

    /**
     * @param int $page
     * @return string
     */
    public function nextEnabled($page): string
    {
        return $this->pageWithTextAndClass($page, $this->option('next_message'), $this->option('css_next_class'), $this->option('rel_next'));
    }

    public function first(): string
    {
        return $this->page(1);
    }

    /**
     * @param int $page
     * @return string
     */
    public function page($page): string
    {
        return $this->pageWithText($page, (string)$page);
    }

    /**
     * @param int $page
     * @param string $text
     * @param string|null $rel
     * @return string
     */
    public function pageWithText($page, $text, ?string $rel = null): string
    {
        return $this->pageWithTextAndClass($page, $text, '', $rel);
    }

    /**
     * @param int $page
     * @return string
     */
    public function last($page): string
    {
        return $this->page($page);
    }

    /**
     * @param int $page
     * @return string
     */
    public function current($page): string
    {
        $text = trim($page . ' ' . $this->option('active_suffix'));

        return $this->spanLi($this->option('css_active_class'), $text);
    }

    public function separator(): string
    {
        return $this->spanLi($this->option('css_dots_class'), $this->option('dots_message'));
    }
}
