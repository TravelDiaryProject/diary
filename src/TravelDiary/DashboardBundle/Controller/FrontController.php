<?php

namespace TravelDiary\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontController extends Controller
{
    public function indexAction()
    {
        return $this->render('TDDashboardBundle:Front:index.html.twig');
    }
}
