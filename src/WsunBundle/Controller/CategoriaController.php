<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Categoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Categorium controller.
 *
 */
class CategoriaController extends Controller
{
    /**
     * Lists all categorium entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository('WsunBundle:Categoria')->findAll();
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $categorias, 
                $request->query->getInt('page', 1),
                $limite
        );
 
        return $this->render('WsunBundle:categoria:index.html.twig', 
            array('pagination' => $pagination));
    }

   

    /**
     * Creates a new categorium entity.
     *
     */
    public function newAction(Request $request)
    {
        $categorium = new Categoria();
        //$categoria->setPadre
        $form = $this->createForm('WsunBundle\Form\CategoriaType', $categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorium);
            $em->flush();

            return $this->redirectToRoute('admin_categoria_show', array('id' => $categorium->getId()));
        }

        return $this->render('WsunBundle:categoria:new.html.twig', array(
            'categorium' => $categorium,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a categorium entity.
     *
     */
    public function showAction(Categoria $categorium)
    {
        $deleteForm = $this->createDeleteForm($categorium);
        $cat='Principal';
        if($categorium->getPadre()!=null && $categorium->getEstado()=='1'){
            $cat=$categorium->getPadre()->getNombreCat();
        }

        return $this->render('WsunBundle:categoria:show.html.twig', array(
            'cat'=>$cat,
            'categorium' => $categorium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing categorium entity.
     *
     */
    public function editAction(Request $request, Categoria $categorium)
    {
        $deleteForm = $this->createDeleteForm($categorium);
        $editForm = $this->createForm('WsunBundle\Form\CategoriaType', $categorium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_categoria_show', array('id' => $categorium->getId()));
           // return $this->redirectToRoute('admin_categoria_edit', array('id' => $categorium->getId()));
        }

        return $this->render('WsunBundle:categoria:edit.html.twig', array(
            'categorium' => $categorium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a categorium entity.
     *
     */
    public function deleteAction(Request $request, Categoria $categorium)
    {
        $form = $this->createDeleteForm($categorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorium);
            $em->flush();
        }

        return $this->redirectToRoute('admin_categoria_index');
    }

    /**
     * Creates a form to delete a categorium entity.
     *
     * @param Categoria $categorium The categorium entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Categoria $categorium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_categoria_delete', array('id' => $categorium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
