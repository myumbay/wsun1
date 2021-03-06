<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Pedido;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Dompdf\Dompdf;
/**
 * @Security("has_role('ROLE_USER')")
 */
class PedidoController extends Controller
{
    private $session;
    public function __construct() {
        $this->session=new Session();
    }
	/**
     * Lists all pedido entities.
     *
     */
    public function indexAction(Request $request)
    {
        
        $user = $this->getUser();
        
            $ActivarNuevo=0;
             $admin=0;
            if(in_array('ROLE_USER', $this->getUser()->getRoles())){
                $ActivarNuevo=1;    
            }else if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
                $admin=1;
            }
			$idEmpresa=0;
			
			if (!is_null($user->getDepartamento())){
				$idEmpresa=$user->getDepartamento()->getIdEmpresa()->getId();
			}
			
       $idUser=$this->getUser()->getId();
	  
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:Pedido', 'ped');
        $qb->select('e.id,e.nombreEmp,ped.codigoPedido');
        $qb->innerJoin('ped.idUsuario', 'u');
        $qb->innerJoin('u.departamento', 'dpt');
        $qb->innerJoin('dpt.idEmpresa', 'e');
        if($admin!= '1' and $idEmpresa>0){
        $qb->andWhere('e.id = :id');
		$qb->andWhere('ped.idUsuario = :idus');    
        $qb->setParameter('idus', $idUser);
        $qb->setParameter('id', $idEmpresa);
        }
		
        $qb->addGroupBy('e.id,e.nombreEmp,ped.codigoPedido');
        $qb->addOrderBy('e.nombreEmp', 'ASC');

        $empresa = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
       

        //$em = $this->getDoctrine()->getManager();
       //$pedidos = $em->getRepository('WsunBundle:Pedido')->findAll();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:Pedido', 'ped');
        $qb->select('e.id as idEmpresa,ped.id, ped.fechaCreacion, ped.codigoPedido,ped.estadoPedido, dpt.nombreDep,e.nombreEmp');
        $qb->innerJoin('ped.idUsuario', 'u');
        $qb->innerJoin('u.departamento', 'dpt');
        $qb->innerJoin('dpt.idEmpresa', 'e');
		if ($ActivarNuevo==1 and $idEmpresa>0 ){
        $qb->andWhere('e.id = :id');
		$qb->andWhere('ped.idUsuario = :idus');    
        $qb->setParameter('idus', $idUser);
        $qb->setParameter('id', $idEmpresa);
		}
        $qb->addOrderBy('ped.codigoPedido', 'DESC');
        
		$pedidos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
		
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidos, 
                $request->query->getInt('page', 1),
                $limite
        );
		
        return $this->render('WsunBundle:pedido:index.html.twig', 
            array('pagination' => $pagination,'empresa'=>$empresa,'activarNuevo'=>$ActivarNuevo));

    }
    public function consultaPorEmpresaAction(Request $request) {
        $id=$request->get('id'); 
        $estado=$request->get('estado');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:Pedido', 'ped');
        $qb->select('e.id as idEmpresa,ped.id, ped.codigoPedido,ped.fechaCreacion,ped.estadoPedido, dpt.nombreDep,e.nombreEmp');
        $qb->innerJoin('ped.idUsuario', 'u');
        $qb->innerJoin('u.departamento', 'dpt');
        $qb->innerJoin('dpt.idEmpresa', 'e');
        if ($estado == '1') {
                $qb->andWhere('ped.estadoPedido = :estado');
                $qb->setParameter('estado', '1');
        } elseif (empty($estado)) {
                $qb->andWhere('ped.estadoPedido IS NULL');
                // $qb->setParameter('estado', 0);}
        }
        if($id>0){
            $qb->andWhere('e.id = :id');
            $qb->setParameter('id', $id);
        }
        $qb->addOrderBy('ped.codigoPedido', 'DESC');
        $pedidos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
		//var_dump($pedidos);die;
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
            $pedidos,
            $request->query->getInt('page', 1),
            $limite
        );

        return $this->render('WsunBundle:pedido:index_filtro.html.twig',
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
        $pedido->setFechaCreacion(new \DateTime('now'));
        $form = $this->createForm('WsunBundle\Form\PedidoType', $pedido,array($rol));
                      
        $form->handleRequest($request);
        $pedidos = $em->getRepository('WsunBundle:Pedido')->findOneBy(array(),array('id' => 'DESC'));
        if(count($pedidos)>0){
            $codigo=$pedidos->getCodigoPedido()+1;
        }else{
            $codigo=100;
        }
        if ($form->isValid()){//$form->isSubmitted() && $form->isValid()) {
            //$fc=$form->getData()->getFechaCreacion();
            //$date = new \DateTime($fc);
            $em = $this->getDoctrine()->getManager();
            
            $pedido->setCodigoPedido($codigo);
           // $pedido->setFechaCreacion($date);
            $pedido->setUpdatedBy(-1);
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
        $empresa=0;
		if (!is_null($pedido->getIdUsuario()->getDepartamento())){
				$empresa=$pedido->getIdUsuario()->getDepartamento()->getIdEmpresa()->getId();
		}
		if ($empresa==0){
                    $mensaje = 'No existe una empresa asociado al usuario, Verifique!!';
                    $this->session->getFlashBag()->add("status",$mensaje);
                    return $this->redirectToRoute('pedido_new');
		}
		
        $em = $this->getDoctrine()->getManager();
        if($pedido->getUpdatedBy()==-1)
        {
            $aprobador='Orden Pendiente';
        }else if($pedido->getUpdatedBy()>0)
        {
            $usuarioAprobado = $em->getRepository('WsunBundle:Usuarios')->find($pedido->getUpdatedBy());
            $aprobador=$usuarioAprobado->getUsername();
        }
        $deleteForm = $this->createDeleteForm($pedido);
        
        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($pedido->getId());
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidosDet, 
                $request->query->getInt('page', 1),
                $limite
        );
        return $this->render('WsunBundle:pedido:show.html.twig', array(
            'id'=>$pedido->getId(),
            'empresa'=>$empresa,
            'aprobador'=>$aprobador,
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
        if($this->getUser()){
                $rol=$this->getUser()->getRoles()[0]->getName();
        }
        
        $deleteForm = $this->createDeleteForm($pedido);
        $editForm = $this->createForm('WsunBundle\Form\PedidoType', $pedido,array($rol));
        $editForm->handleRequest($request);
   
        //var_dump($editForm->getData()->getFechaCreacion());die;
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $pedido->setUpdatedBy($this->getUser()->getId());
            $em = $this->getDoctrine()->getManager();
            $em->persist($pedido);
            $em->flush();
            //$this->getDoctrine()->getManager()->flush();

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
        
        if($pedido->getUpdatedBy()==-1)
        {
            $aprobador='Orden Pendiente';
        }else if($pedido->getUpdatedBy()>0)
        {
            $usuarioAprobado = $em->getRepository('WsunBundle:Usuarios')->find($pedido->getUpdatedBy());
            $aprobador=$usuarioAprobado->getUsername();
        }
        
        $pedidosDet = $em->getRepository('WsunBundle:DetallePedido')->findByIdPedido($pedido->getId());
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $pedidosDet, 
                $request->query->getInt('page', 1),
                $limite
        );

        
        $html = $this->renderView('WsunBundle:detallepedido:mail.html.twig', array(
            'pedido' => $pedido,
            'aprobador'=>$aprobador,
            'pagination' => $pagination
            )
        );
       $dompdf = new Dompdf();
       $dompdf->loadHtml($html);
       // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream();
        return new Response($dompdf->stream()
           /* $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="fichero.pdf"'
            )*/
        );


    }
    
}
