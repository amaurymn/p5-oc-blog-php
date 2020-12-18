<?php

namespace App\Services\Paginator;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

class Paginator
{
    private Pagerfanta $pagerfanta;
    private PagerView $pagerView;
    private string $pager;
    private string $path;

    public function __construct(array $items)
    {
        $this->pagerView  = new PagerView();
        $this->pagerfanta = new Pagerfanta(new ArrayAdapter($items));
    }

    /**
     * @param int $currentPage
     * @param int $maxPerPage
     * @return iterable|null
     */
    public function paginateItems(int $currentPage, int $maxPerPage = 3): ?iterable
    {
        $this
            ->pagerfanta->setNormalizeOutOfRangePages(true)
                        ->setMaxPerPage($maxPerPage)
                        ->setCurrentPage($currentPage);

        $this->setPager();

        return $this->pagerfanta->getCurrentPageResults();
    }

    /**
     * @return string|null
     */
    public function getPager(): ?string
    {
        return ($this->pagerfanta->getNbPages() > 1) ? $this->pager : null;
    }

    /**
     * @param string $path
     * @return Paginator
     */
    public function setPath(string $path): Paginator
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    private function setPager(): string
    {
        $routeGenerator = function ($currentPage) {
            return $this->path . $currentPage;
        };

        $options = [
            'proximity'    => 1,
            'prev_message' => '<i class="fas fa-chevron-left"></i>',
            'next_message' => '<i class="fas fa-chevron-right"></i>',
        ];

        return $this->pager = $this->pagerView->render($this->pagerfanta, $routeGenerator, $options);
    }
}
