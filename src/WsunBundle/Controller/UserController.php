<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Usuarios;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('WsunBundle:Usuarios')->findAll();
		
		//var_dump($users->getUserRoles());die;
		
		
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $users, 
                $request->query->getInt('page', 1),
                $limite
        );
 
        return $this->render('WsunBundle:user:index.html.twig', 
            array('pagination' => $pagination));

    }

    /**
     * Creates a new user entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = new Usuarios();
        //$request = $this->getRequest();
               
        $form = $this->createForm('WsunBundle\Form\UsuariosType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->setSecurePassword($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('admin_user_index', array('id' => $user->getId()));
        }

        return $this->render('WsunBundle:user:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     */
    public function showAction(Usuarios $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('WsunBundle:user:show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     */
    public function editAction(Request $request, Usuarios $user)
    {
        if (!$user) {
         throw $this->createNotFoundException('Usuario No existe');
		}
		 $em = $this->getDoctrine()->getManager();
		$originalPassword = $user->getPassword(); 
		$deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('WsunBundle\Form\UsuariosType', $user);
		//$editForm->get('empresa')->setData($em->getReference('WsunBundle:Empresa', 1));	
        $current_pass = $user->getPassword();
        $editForm->handleRequest($request);

       if ($editForm->isSubmitted() && $editForm->isValid()) {
			$plainPassword = $editForm->get('password')->getData();
			  if (!empty($plainPassword))  {  
                //encode the password   
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user); //get encoder for hashing pwd later
                $tempPassword = $encoder->encodePassword($user->getPassword(), $user->getSalt()); 
                $user->setPassword($tempPassword);                
            }
            else {
                $user->setPassword($originalPassword);
            }
            //evalua si la contraseña fue modificada: ------------------------
            /*if ($current_pass != $user->getPassword()) {
                $this->setSecurePassword($user);
            }*/
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user_index', array('id' => $user->getId()));
        }

        return $this->render('WsunBundle:user:edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     */
    public function deleteAction(Request $request, Usuarios $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Usuarios $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    private function setSecurePassword(&$entity) {
    $entity->setSalt(md5(time()));
    $encoder = new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder('sha512', true, 10);
    $password = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
    $entity->setPassword($password);
}
}
