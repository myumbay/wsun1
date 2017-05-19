<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Pedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Pedido controller.
 *
 */
class PedidoController extends Controller
{
    /**
     * Lists all pedido entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $pedidos = $em->getRepository('WsunBundle:Pedido')->findAll();

        return $this->render('WsunBundle:pedido:index.html.twig', array(
            'pedidos' => $pedidos,
        ));
    }

    /**
     * Creates a new pedido entity.
     *
     */
    public function newAction(Request $request)
    {
        $pedido = new Pedido();
        $form = $this->createForm('WsunBundle\Form\PedidoType', $pedido);
        $form->handleRequest($request);
        
        //$data = $form->getData();
        //$user = $this->getUser();
        //var_dump($user);
        if ($form->isValid()){//$form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($pedido);
            $em->flush();

            return $this->redirectToRoute('pedido_show', array('id' => $pedido->getId()));
        }

        return $this->render('WsunBundle:pedido:new.html.twig', array(
            'pedido' => $pedido,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pedido entity.
     *
     */
    public function showAction(Pedido $pedido)
    {
        $deleteForm = $this->createDeleteForm($pedido);

        return $this->render('WsunBundle:pedido:show.html.twig', array(
            'pedido' => $pedido,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pedido entity.
     *
     */
    public function editAction(Request $request, Pedido $pedido)
    {
        $deleteForm = $this->createDeleteForm($pedido);
        $editForm = $this->createForm('WsunBundle\Form\PedidoType', $pedido);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('pedido_edit', array('id' => $pedido->getId()));
        }

        return $this->render('WsunBundle:pedido:edit.html.twig', array(
            'pedido' => $pedido,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a pedido entity.
     *
     */
    public function deleteAction(Request $request, Pedido $pedido)
    {
        $form = $this->createDeleteForm($pedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($pedido);
            $em->flush();
        }

        return $this->redirectToRoute('pedido_index');
    }

    /**
     * Creates a form to delete a pedido entity.
     *
     * @param Pedido $pedido The pedido entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Pedido $pedido)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('pedido_delete', array('id' => $pedido->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
        public function indexPedidoAction(Request $request,Pedido $pedido){
        $id=$pedido->getId();
        $em = $this->getDoctrine()->getManager();

        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findById($id);

       return $this->render('WsunBundle:pedido:indexPedido.html.twig', array(
            'pedidosDet' => $pedidosDet,
        ));
    }
}
