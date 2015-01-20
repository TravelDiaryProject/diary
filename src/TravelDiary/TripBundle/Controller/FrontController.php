<?php

namespace TravelDiary\TripBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('TDTripBundle:Front:index.html.twig');
    }
}
