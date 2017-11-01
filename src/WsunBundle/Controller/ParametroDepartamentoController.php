<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\ParametroDepartamento;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
     * @Security("has_role('ROLE_ADMIN')")
    */
class ParametroDepartamentoController extends Controller
{
    /**
     * Lists all parametroDepartamento entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $empresas = $em->getRepository('WsunBundle:ParametroDepartamento')->findAll();
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $empresas, 
                $request->query->getInt('page', 1),
                $limite
        );
 
        return $this->render('WsunBundle:parametrodepartamento:index.html.twig',
                array('parametroDepartamentos' => $pagination));
    }

    /**
     * Creates a new parametroDepartamento entity.
     *
     */
    public function newAction(Request $request)
    {
        $parametroDepartamento = new Parametrodepartamento();
        $form = $this->createForm('WsunBundle\Form\ParametroDepartamentoType', $parametroDepartamento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($parametroDepartamento);
            $em->flush();

            return $this->redirectToRoute('parametrodepartamento_show', array('id' => $parametroDepartamento->getId()));
        }

        return $this->render('WsunBundle:parametrodepartamento:new.html.twig', array(
            'parametroDepartamento' => $parametroDepartamento,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a parametroDepartamento entity.
     *
     */
    public function showAction(ParametroDepartamento $parametroDepartamento)
    {
        $deleteForm = $this->createDeleteForm($parametroDepartamento);

        return $this->render('WsunBundle:parametrodepartamento:show.html.twig', array(
            'parametroDepartamento' => $parametroDepartamento,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing parametroDepartamento entity.
     *
     */
    public function editAction(Request $request, ParametroDepartamento $parametroDepartamento)
    {
        $deleteForm = $this->createDeleteForm($parametroDepartamento);
        $editForm = $this->createForm('WsunBundle\Form\ParametroDepartamentoType', $parametroDepartamento);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parametrodepartamento_edit', array('id' => $parametroDepartamento->getId()));
        }

        return $this->render('WsunBundle:parametrodepartamento:edit.html.twig', array(
            'parametroDepartamento' => $parametroDepartamento,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a parametroDepartamento entity.
     *
     */
    public function deleteAction(Request $request, ParametroDepartamento $parametroDepartamento)
    {
        $form = $this->createDeleteForm($parametroDepartamento);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parametroDepartamento);
            $em->flush();
        }

        return $this->redirectToRoute('parametrodepartamento_index');
    }

    /**
     * Creates a form to delete a parametroDepartamento entity.
     *
     * @param ParametroDepartamento $parametroDepartamento The parametroDepartamento entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ParametroDepartamento $parametroDepartamento)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parametrodepartamento_delete', array('id' => $parametroDepartamento->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
