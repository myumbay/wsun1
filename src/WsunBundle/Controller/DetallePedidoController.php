<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\DetallePedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Detallepedido controller.
 *
 */
class DetallePedidoController extends Controller
{
    /**
     * Lists all detallePedido entities.
     *
     */
    public function indexAction(Request $request)
    {
      
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();

        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findById($id);

       return $this->render('WsunBundle:detallepedido:index.html.twig', array(
            'pedidosDet' => $pedidosDet,
        ));
    }

    /**
     * Creates a new detallePedido entity.
     *
     */
    public function newAction(Request $request)
    {
        $detallePedido = new Detallepedido();
        $form = $this->createForm('WsunBundle\Form\DetallePedidoType', $detallePedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($detallePedido);
            $em->flush();

            return $this->redirectToRoute('detallepedido_show', array('id' => $detallePedido->getId()));
        }

        return $this->render('detallepedido/new.html.twig', array(
            'detallePedido' => $detallePedido,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a detallePedido entity.
     *
     */
    public function showAction(DetallePedido $detallePedido)
    {
        $deleteForm = $this->createDeleteForm($detallePedido);

        return $this->render('detallepedido/show.html.twig', array(
            'detallePedido' => $detallePedido,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing detallePedido entity.
     *
     */
    public function editAction(Request $request, DetallePedido $detallePedido)
    {
        $deleteForm = $this->createDeleteForm($detallePedido);
        $editForm = $this->createForm('WsunBundle\Form\DetallePedidoType', $detallePedido);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('detallepedido_edit', array('id' => $detallePedido->getId()));
        }

        return $this->render('detallepedido/edit.html.twig', array(
            'detallePedido' => $detallePedido,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a detallePedido entity.
     *
     */
    public function deleteAction(Request $request, DetallePedido $detallePedido)
    {
        $form = $this->createDeleteForm($detallePedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($detallePedido);
            $em->flush();
        }

        return $this->redirectToRoute('detallepedido_index');
    }

    /**
     * Creates a form to delete a detallePedido entity.
     *
     * @param DetallePedido $detallePedido The detallePedido entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(DetallePedido $detallePedido)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('detallepedido_delete', array('id' => $detallePedido->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
