<?php

namespace DondrekielAppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DondrekielAppBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
