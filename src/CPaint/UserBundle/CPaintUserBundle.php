<?php

namespace CPaint\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CPaintUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
