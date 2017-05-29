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
public function loginAction(Request $request) {
    //Llamamos al servicio de autenticacion
    $authenticationUtils = $this->get('security.authentication_utils');
     
    // conseguir el error del login si falla
    $error = $authenticationUtils->getLastAuthenticationError();
 
    // ultimo nombre de usuario que se ha intentado identificar
    $lastUsername = $authenticationUtils->getLastUsername();
     
    return $this->render(
            'Wsun:Admin:login.html.twig', array(
                'last_username' => $lastUsername,
                'error' => $error,
            ));
}


}
