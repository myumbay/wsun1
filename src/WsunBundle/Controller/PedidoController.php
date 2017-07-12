<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Pedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function indexAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $pedidos = $em->getRepository('WsunBundle:Pedido')->findAll();
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidos, 
                $request->query->getInt('page', 1),
                $limite
        );
 
        return $this->render('WsunBundle:pedido:index.html.twig', 
            array('pagination' => $pagination));

    }

    /**
     * Creates a new pedido entity.
     *
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rol='';
        if($this->getUser())
            $rol=$this->getUser()->getRoles()[0]->getName();
        
        
        $pedido = new Pedido();
        $form = $this->createForm('WsunBundle\Form\PedidoType', $pedido,array($rol));
        $form->handleRequest($request);
        $pedidos = $em->getRepository('WsunBundle:Pedido')->findOneBy(array(),array('id' => 'DESC'));
        $codigo=$pedidos->getCodigoPedido()+1;
        
        if ($form->isValid()){//$form->isSubmitted() && $form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
            $pedido->setCodigoPedido($codigo);
            $em->persist($pedido);
            $em->flush();

            return $this->redirectToRoute('pedido_show', array('id' => $pedido->getId()));
        }

        return $this->render('WsunBundle:pedido:new.html.twig', array(
            'codigo'=>$codigo,
            'pedido' => $pedido,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a pedido entity.
     *
     */
    public function showAction(Request $request,Pedido $pedido)
    {
        //var_dump($pedido->getIdUsuario()->getDepartamento()->getIdEmpresa()->getNombreEmp());die;
        $deleteForm = $this->createDeleteForm($pedido);
        $em = $this->getDoctrine()->getManager();
        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($pedido->getId());
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidosDet, 
                $request->query->getInt('page', 1),
                $limite
        );
        return $this->render('WsunBundle:pedido:show.html.twig', array(
            'pedido' => $pedido,
            'pagination' => $pagination,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing pedido entity.
     *
     */
    public function editAction(Request $request, Pedido $pedido)
    {
        $rol='';
       // var_dump($this->getUser()->getRoles()[0]->getName()   );die;
        if($this->getUser()){
                $rol=$this->getUser()->getRoles()[0]->getName();
        }
        
        $deleteForm = $this->createDeleteForm($pedido);
        $editForm = $this->createForm('WsunBundle\Form\PedidoType', $pedido,array($rol));
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
        
        $em = $this->getDoctrine()->getManager();
        if($pedido->getEstadoPedido()==true){
            $mensaje = 'No se puede eliminar debido a que ya se acepto el pedido';
            $this->session->getFlashBag()->add("status",$mensaje);
            return $this->redirectToRoute('pedido_index');
        }else{
           $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($pedido->getId());
            foreach ($pedidosDet as $enty) {
            $em->remove($enty);
                }
            $em->remove($pedido);
            $em->flush();    
            //$em->flush();
        }
                
//        $form = $this->createDeleteForm($pedido);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            
//            $em->remove($pedido);
//            $em->flush();
//        }

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
    public function exportarPdfAction(Request $request){
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $pedido = $em->getRepository('WsunBundle:Pedido')->find($id);
        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($pedido->getId());
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidosDet, 
                $request->query->getInt('page', 1),
                $limite
        );
//        return $this->render('WsunBundle:pedido:show.html.twig', array(
//            'pedido' => $pedido,
//            'pagination' => $pagination,
//            'delete_form' => $deleteForm->createView(),
//        ));
        
        
        
        $html = $this->renderView('WsunBundle:detallepedido:mail.html.twig', array(
            'pedido' => $pedido,
            'pagination' => $pagination
            )
        );

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="fichero.pdf"'
            )
        );


    }
    
}
