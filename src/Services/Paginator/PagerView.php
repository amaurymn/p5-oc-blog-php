<?php

namespace App\Services\Paginator;

use Pagerfanta\View\DefaultView;

class PagerView extends DefaultView
{
    public function getName(): string
    {
        return 'custom_default';
    }

    protected function createDefaultTemplate(): BootstrapTemplate
    {
        return new BootstrapTemplate();
    }
}
