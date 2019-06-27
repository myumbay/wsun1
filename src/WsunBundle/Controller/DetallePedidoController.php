<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\DetallePedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Dompdf\Dompdf;
/**
* @Security("has_role('ROLE_USER')")
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
        $user = $this->getUser();
        $idUser=$user->getId();
        $em = $this->getDoctrine()->getManager();
        $usuario = $em->getRepository('WsunBundle:Usuarios')->find($idUser);
        $idEmpresa=$usuario->getDepartamento()->getIdEmpresa()->getId();
        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($id);
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidosDet, 
                $request->query->getInt('page', 1),
                $limite
        );
       
        return $this->render('WsunBundle:detallepedido:index.html.twig', array(
           'id'=>$id,
           'idEmpresa'=>$idEmpresa,
           'pagination' => $pagination,
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
    
    public function aceptarOrdenAction(Request $request)
    {
        $id=$request->get('id');
        $user = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $pedidos = $em->getRepository('WsunBundle:Pedido')->find($id);
        $pedidos->setEstadoPedido(1);
        $pedidos->setUpdatedBy($user);
        $em->persist($pedidos);
        $em->flush();
        
        $userId=$pedidos->getUpdatedBy();
        $usuario = $em->getRepository('WsunBundle:Usuarios')->findBy(array('ruc'=> '123456789'),1);
        $correo=$usuario->getCorreo();
        /* @var $correo \WsunBundle\ComunBundle\Services\Correo */
        $correoE = $this->get('sistema_de_correos');
        //$correoE->enviarPrueba($email);
        $enviar=$correoE->aceptarOrden($correo,$pedidos->getCodigoPedido());
        $response = new Response(json_encode(array(
                            'mensaje' => 'Ud ha aceptado la orden',
                                )
                        )
                );
                $response->headers->set('Content-Type', 'application/json');
                return $response;            
      
    }
    /**
     * Displays a form to edit an existing detallePedido entity.
     *
     */
    public function editAction(Request $request, DetallePedido $detallePedido)
    {
        $id=$request->get('id');
        $id_empresa=$detallePedido->getIdProducto()->getEmpresa()->getId();
        $deleteForm = $this->createDeleteForm($detallePedido);
        $editForm = $this->createForm('WsunBundle\Form\DetallePedidoType', $detallePedido,array($id_empresa,'action'=>$this->generateUrl('detallepedido_edit',array('id'=>$detallePedido->getId()))));
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
        $id=$detallePedido->getIdPedido()->getId();
        $form = $this->createDeleteForm($detallePedido);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($detallePedido);
            $em->flush();
        }
        return $this->redirectToRoute('detallepedido_index', array('id' => $id));
       // return $this->redirectToRoute('detallepedido_index');
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
        $iva = $em->getRepository('WsunBundle:Parametro')->findOneByDescripcion('IVA');
        $ivaValor=$iva->getValor();
        
        $in = $em->createQueryBuilder()
            ->select('ep')
            ->from('WsunBundle:EmpresaProducto','ep')
            ->where('ep.empresa=:id')
            ->setParameter('id', $id);
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
             $idprod[$i]=$det[$i]->getIdProducto()->getProducto()->getId();

         }
        }

        return $this->render('WsunBundle:detallepedido:addPedido.html.twig',array('productos' => $pem,'idPedido'=>$idPedido,'prod'=>$idprod,'det'=>$det,'ivaValor'=>$ivaValor));
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
		
		$username = $this->getUser()->getUsername();
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
        $us=$pedido->getIdUsuario()->getDepartamento()->getIdEmpresa()->getId();
        $responsable=$pedido->getIdUsuario()->getDepartamento()->getResponsable();
        $departamento=$pedido->getIdUsuario()->getDepartamento()->getNombreDep();
       
        $codigo=$pedido->getCodigoPedido();
		
     	$sql = " 
         SELECT correo
          FROM usuarios u inner join user_role ur on u.id=ur.user_id
          inner join roles r on r.id=ur.role_id
          inner join departamento d on d.id=u.id_departamento
          inner join empresa e on e.id=d.id_empresa 
          where r.nombre='ROLE_ACEPTAR_PEDIDO' and e.id=".$us." limit 1";
    $em = $this->getDoctrine()->getManager();
    $stmt = $em->getConnection()->prepare($sql);
    $stmt->execute();
    $r=$stmt->fetchAll();
	//var_dump($r);die;
    if(count($r)>0){
        $mailManager=$r[0]['correo'];
    
    }else{
        $response = new Response(json_encode(array('error' => 0, 'mensaje' => 'No existe un manager Registrado en la empresa ')));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        }

		
    if (is_array($idsProductos) && count($idsProductos) > 0) {
       
        
			for($i=0;$i<count($idsProductos);$i++)
            {
                $detallePedido = $em->getRepository('WsunBundle:DetallePedido')->findBy(array('idProducto' => $idsProductos[$i],'idPedido' =>$pedido_id));
             
                if($detallePedido)
                {
                    $detallePedido = $detallePedido[0];
                    if($pedido->getEstadoPedido() ==true){
                   
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
			
            $mensaje='Lista de orden Guardada';
        /* @var $correo \WsunBundle\Services\Correo */
        $correoE = $this->get('sistema_de_correos');
        //$correoE->enviarPrueba('carolyumbay@gmail.com');
		
/*******************Creación de pdf para envío de correo***********************/	
/*Por: Ing. Carolina Yumbay****************************************************
/*Fecha:05/06/2019*/
		$em = $this->getDoctrine()->getManager(); 
		/* @var $qb \Doctrine\ORM\QueryBuilder */	
		$pedidosDet= $em->getRepository('WsunBundle:DetallePedido')->findBy(array('idPedido' => $pedido->getId()));
	    //$pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($pedido->getId());
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidosDet, 
                $request->query->getInt('page', 1),
                $limite
        );
		
        $aprobador='Orden Generada';
		$html = $this->renderView('WsunBundle:detallepedido:mail.html.twig', array(
            'pedido' => $pedido,
            'aprobador'=>$aprobador,
            'pagination' => $pagination
            )
        );	
		
		//if (file_exists('../Documentos/pedido/pedido'.date('YmdGis').'.pdf')) {
			//unlink('../Documentos/pedido/pedido'.date('YmdGis').'.pdf');
		//} 
			
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->render();
		$output = $dompdf->output();
		file_put_contents("../Documentos/pedido/pedido".date('YmdGis').".pdf", $output);

		/*******************Fin*******************************************************/
		
        $enviar=$correoE->nuevaOrden($departamento,$responsable,$mailManager,$codigo,'../Documentos/pedido/pedido'.date('YmdGis').'.pdf');
        if($enviar=true )
                {      
                $mensaje = 'El mensaje se ha enviado correctamente';
                $this->session->getFlashBag()->add("status",$mensaje);
              
                }else {
                 $mensaje = 'El mensaje no se ha enviado correctamente, existen datos incorrectos!!';
                $this->session->getFlashBag()->add("status",$mensaje);
               
            }    
            
        }
           
         } catch (\Exception $e) {
            $mensaje = "Error al Guardar los datos.".$e->getMessage().$e->getLine();
        }    
               
        $response = new Response(json_encode(array('error' => 1,'mensaje' => $mensaje)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

