<?php

namespace TravelDiary\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TDUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
