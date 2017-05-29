<?php

namespace WsunBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WsunBundle:Default:index.html.twig');
    }
     public function contactosAction(Request $request)
    {
        
         
        return $this->render('WsunBundle:Default:contactos.html.twig');
    }
}
