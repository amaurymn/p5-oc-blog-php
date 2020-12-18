<?php

namespace App\Services\Paginator;

use Pagerfanta\View\DefaultView;

class PagerView extends DefaultView
{
    public function getName()
    {
        return 'custom_default';
    }

    protected function createDefaultTemplate()
    {
        return new BootstrapTemplate();
    }
}
