<?php
namespace WsunBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core;
 
class SecurityController extends Controller
{

    public function loginAction(Request $request)
    {
        //$request=$this->get('request_stack')->getCurrentRequest();
        //$session = $request->getSession();
        $autenticationUtils = $this->get("security.authentication_utils");
        $lastUsername = $autenticationUtils->getLastUsername();
        $error = $autenticationUtils->getLastAuthenticationError();
        
        return $this->render('WsunBundle:Security:login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }
//    public function checkAction(Request $request) {
//        $session = $request->getSession();
//        // The security layer will NOT intercept this request
//        if ($session->get('_security.secured_area.target_path')) {
//            return $this->redirect($session->get('_security.secured_area.target_path'));
//        } else {
//            return $this->redirect($this->generateUrl('login2'));
//        }
//    }
//    /**
//     * @Route("/logout", name="logout")
//     */
//    public function logoutAction()
//    {
//        $this->container->get('security.token_storage')->setToken(null);
//
//        return $this->redirect($this->generateUrl('login'));
//    }
}

