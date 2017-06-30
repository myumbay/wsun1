<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\DetallePedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
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
        $idEmpresa=$request->get('idEmpresa');
        $em = $this->getDoctrine()->getManager();
        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($id);
        return $this->render('WsunBundle:detallepedido:index.html.twig', array(
           'id'=>$id,
           'idEmpresa'=>$idEmpresa,
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
            $detPed=$em->getRepository('WsunBundle:DetallePedido')->findBy(array('idProducto' => $form->getData()->getIdProducto()->getId(),'idPedido'=>$pedido->getId()));
            if(count($detPed)>0)
                {      
                $mensaje = 'Ya existe un producto para este pedido';
                $this->session->getFlashBag()->add("status",$mensaje);
                return $this->redirectToRoute('detallepedido_new',array('id'=>$id));

                }
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
    public function addPedidoAction(Request $request,$id)
    {
        $idPedido=$request->get('idPedido');
        $em = $this->getDoctrine()->getManager();
        $in = $em->createQueryBuilder()
            ->select('ep')
            ->from('WsunBundle:EmpresaProducto','ep')
            ->where('ep.empresa=:slug')
            ->setParameter('slug', $id);
        $pem=$in->getQuery()->getResult();
        
        $em = $this->getDoctrine()->getManager();
        $in = $em->createQueryBuilder()
            ->select('dp')
            ->from('WsunBundle:DetallePedido','dp')
            ->where('dp.idPedido=:id')
            ->setParameter('id', $idPedido);
        $det=$in->getQuery()->getResult();
        
        $idprod='';
        if(count($det)>0)
        {
         for($i=0;$i< count($det);$i++)
         {
             $idprod[]=$det[$i]->getIdProducto()->getProducto()->getId();

         }
        }

        return $this->render('WsunBundle:detallepedido:addPedido.html.twig',array('productos' => $pem,'idPedido'=>$idPedido,'prod'=>$idprod,'det'=>$det));
    }
     public function DetalleGuardarAction(Request $request)
    {
       try{
        $mensaje = "";
        $pedido_id = $request->request->get('pedido_id');
        $idsProductos = $request->request->get('ids_productos');
        $capacidades = trim($request->request->get('capacidades'));
        $ivas = trim($request->request->get('ivas'));
        $pu = trim($request->request->get('pu'));
        $vt = trim($request->request->get('vt'));
       
        if ($capacidades == '' || $idsProductos == '' || $ivas == '' || $pu == ''|| $vt == '') {
                    $response = new Response(json_encode(array('error' => 1, 'mensaje' => 'LOS DATOS PROPORCIONADOS SON INCORRECTOS')));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
        
         if ($pedido_id == '') {
                $response = new Response(json_encode(array('error' => 1, 'mensaje' => 'NO EXISTE EL PEDIDO')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        $idsProductos = explode(',', $idsProductos);
        $capacidades = explode(',', $capacidades);
        $ivas = explode(',', $ivas);
        $pu = explode(',', $pu);
        $vt = explode(',', $vt);
        $contador = 0;
        foreach ($idsProductos as $ids) {
                    $capacidadProducto[$ids] = $capacidades[$contador];
                    $ivasProducto[$ids] = $ivas[$contador];
                    $puProducto[$ids] = $pu[$contador];
                    $puProducto[$ids] = $vt[$contador];
                    $contador ++;
                }
        $proNoEncontrados = '';
        $productos = 0;
        $em = $this->getDoctrine()->getManager();
        $pedido=$em->getRepository('WsunBundle:Pedido')->find($pedido_id);
    
        if (is_array($idsProductos) && count($idsProductos) > 0) {
        
            for($i=0;$i<count($idsProductos);$i++)
            {
                $detallePedido = $em->getRepository('WsunBundle:DetallePedido')->findBy(array('idProducto' => $idsProductos[$i],'idPedido' =>$pedido_id));
             
                if($detallePedido)
                {
                    $detallePedido = $detallePedido[0];
                    if($pedido->getEstadoPedido() ==true){
                    //if($Emproductos->getCapacidad()>$capacidades[$i]){
                        $response = new Response(json_encode(array('error' => 0, 'mensaje' => 'No guardados!! El pedido ya fue autorizado no puede cambiar la orden ')));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }else {
                        $em = $this->getDoctrine()->getManager();
                        $entity = $em->getRepository('WsunBundle:DetallePedido')->findBy(array('idPedido' => $pedido->getId()));

                        foreach ($entity as $enty) {
                        $em->remove($enty);
                            }
                        $em->flush();
                    }
                }
               
                    $prod= $em->getRepository('WsunBundle:EmpresaProducto')->find($idsProductos[$i]);
                    $empPr = new DetallePedido();
                    $empPr->setIdPedido($pedido);
                    $empPr->setIdProducto($prod);
                    $empPr->setCodigo($prod->getProducto()->getCodigoProducto());
                    $empPr->setCantidad($capacidades[$i]);
                    $empPr->setValorUnitario($pu[$i]);
                    $empPr->setValorTotal($vt[$i]);
                    $empPr->setObservaciones($ivas[$i]);
                    
                    //$empPr->setCreated($hoy);
                    $em->persist($empPr);

            }
            $em->flush();
            
                $mensaje = 'Pedido Guardado';
                $this->session->getFlashBag()->add("status",$mensaje);
                $response = new Response(json_encode(array('error' => 0,'mensaje' => $mensaje)));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
//                return $this->redirectToRoute('detallepedido_index',array('id'=>$pedido->getId()));
                
        }
           
         } catch (\Exception $e) {
            $mensaje = "Error al Guardar los datos.";
        }    
               
        $response = new Response(json_encode(array('error' => 1,'mensaje' => $mensaje)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
