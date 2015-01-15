<?php

namespace TravelDiary\TipBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('TDTripBundle:Default:index.html.twig');
    }
}
