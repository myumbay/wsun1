<?php
namespace WsunBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * User
 */

class AdminController extends Controller
{
    public function ReportesAction(Request $request) {
        return $this->render('WsunBundle:Admin:reportes.html.twig');
    }
    public function EmpresaAction(Request $request) {
        return $this->render('WsunBundle:Admin:reportes.html.twig');
    }

}
