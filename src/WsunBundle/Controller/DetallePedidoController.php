<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\DetallePedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Detallepedido controller.
 *
 */
class DetallePedidoController extends Controller
{
    private $session;
    public function __construct() {
        $this->session=new Session();
    }
    public function indexAction(Request $request)
    {
         
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($id);
        return $this->render('WsunBundle:detallepedido:index.html.twig', array(
           'id'=>$id,
            'pedidosDet' => $pedidosDet,
        ));
    }

    public function newAction(Request $request)
    {
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $id_empresa=$this->getUser()->getDepartamento()->getIdEmpresa()->getId();
        $prod = $em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('empresa'=>$id_empresa));
  
        $pedido = $em->getRepository('WsunBundle:Pedido')->find($id);
        $detallePedido = new Detallepedido();
        $form = $this->createForm('WsunBundle\Form\DetallePedidoType', $detallePedido,array($id_empresa));
        $form->handleRequest($request);
         if ($form->isSubmitted() ) {
             var_dump($form->getData());die;
            $detPed=$em->getRepository('WsunBundle:DetallePedido')->findBy(array('idProducto' => $form->getData()->getIdProducto()->getId(),'idPedido'=>$pedido->getId()));
            if(count($detPed)>0)
                {      
                $mensaje = 'Ya existe un producto para este pedido';
                $this->session->getFlashBag()->add("status",$mensaje);
                return $this->redirectToRoute('detallepedido_new',array('id'=>$id));

                }
             $detallePedido->setIdProducto(2);
             $detallePedido->setIdPedido($pedido); 
             $em->persist($detallePedido);
             $em->flush();
             return $this->redirectToRoute('detallepedido_index', array('id' => $detallePedido->getIdPedido()->getId()));
            //return $this->redirectToRoute('detallepedido_show', array('id' => $detallePedido->getId()));
        }

        return $this->render('WsunBundle:detallepedido:new.html.twig', array(
            'prod'=>$prod,
            'id'=>$id,
            'detallePedido' => $detallePedido,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a detallePedido entity.
     *
     */
    public function showAction(Request $request,DetallePedido $detallePedido)
    {
         $id=$request->get('id');
        $deleteForm = $this->createDeleteForm($detallePedido);

        return $this->render('WsunBundle:detallepedido:show.html.twig', array(
            'id'=>$id,
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
        $id=$request->get('id');
        $deleteForm = $this->createDeleteForm($detallePedido);
        $editForm = $this->createForm('WsunBundle\Form\DetallePedidoType', $detallePedido,array('action'=>$this->generateUrl('detallepedido_edit',array('id'=>$detallePedido->getId()))));
        $editForm->handleRequest($request);
         if ($editForm->isSubmitted()) {
            
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('detallepedido_index', array('id' => $detallePedido->getIdPedido()->getId()));
        }

        return $this->render('WsunBundle:detallepedido:edit.html.twig', array(
            'id'=>$detallePedido->getIdPedido()->getId(),
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
