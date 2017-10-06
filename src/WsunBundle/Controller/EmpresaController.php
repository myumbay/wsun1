<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Empresa;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WsunBundle\Entity\EmpresaProducto;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class EmpresaController extends Controller
{
    /**
     * Lists all empresa entities.
     *
     */
    private $session;
    public function __construct() {
        $this->session=new Session();
    }
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $empresas = $em->getRepository('WsunBundle:Empresa')->findAll();
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
                $empresas, 
                $request->query->getInt('page', 1),
                $limite
        );
 
        return $this->render('WsunBundle:empresa:index.html.twig',
                array('pagination' => $pagination));
//        return $this->render('WsunBundle:empresa:index.html.twig', array(
//            'empresas' => $empresas,
//        ));
    }

   
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function newAction(Request $request)
    {
        $empresa = new Empresa();
        $form = $this->createForm('WsunBundle\Form\EmpresaType', $empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($empresa);
            $em->flush();

            return $this->redirectToRoute('empresa_show', array('id' => $empresa->getId()));
        }

        return $this->render('WsunBundle:empresa:new.html.twig', array(
            'empresa' => $empresa,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a empresa entity.
     *
     */
    public function showAction(Empresa $empresa)
    {
        $deleteForm = $this->createDeleteForm($empresa);

        return $this->render('WsunBundle:empresa:show.html.twig', array(
            'empresa' => $empresa,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction(Request $request, Empresa $empresa)
    {
        $deleteForm = $this->createDeleteForm($empresa);
        $editForm = $this->createForm('WsunBundle\Form\EmpresaType', $empresa);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('empresa_edit', array('id' => $empresa->getId()));
        }

        return $this->render('WsunBundle:empresa:edit.html.twig', array(
            'empresa' => $empresa,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
   /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Empresa $empresa)
    {
        $form = $this->createDeleteForm($empresa);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($empresa);
            $em->flush();
        }

        return $this->redirectToRoute('empresa_index');
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    private function createDeleteForm(Empresa $empresa)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('empresa_delete', array('id' => $empresa->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
      public function empresaAutocompleteAction(Request $request) {
        $query = $request->get('query');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:Empresa', 'emp');
        $qb->select('emp.nombreEmp, emp.ruc, emp.id');
        $qb->andWhere($qb->expr()->like($qb->expr()->lower('emp.nombreEmp'), $qb->expr()->lower(":nombre")));
        $qb->orWhere($qb->expr()->like($qb->expr()->lower('emp.ruc'), $qb->expr()->lower(":nombre")));
        $qb->setParameter('nombre', "%{$query}%");
        
        $qb->setMaxResults(20);
        $rows = $qb->getQuery()->execute();
        
        $results = array();
        foreach ($rows as $row) {
            $results[$row['id']] = array('value' => "{$row['nombreEmp']} ({$row['ruc']})", 'data' => $row['id']);
        }
        $response = new Response(json_encode(array('query' => $query, 'suggestions' => $results)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function EmpresaGuardarAction(Request $request)
    {
       $idempresa=$request->get('empresa_id');
      
        try{
            if($empresa='' )
                {      
                $mensaje = 'No existe la empresa seleccionada';
                $this->session->getFlashBag()->add("status",$mensaje);
                return $this->redirectToRoute('admin_productos_empresa_index');

                }else{
    
        $em = $this->getDoctrine()->getManager();
        $empresa=$em->getRepository('WsunBundle:Empresa')->find($idempresa);
        $empresaProducto=$em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('empresa'=>$idempresa));
        if(count($empresaProducto)>0) {
            foreach ($empresaProducto as $k => $val) {
                $ep = $em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('id' => $val->getId()));
                $ep = $ep[0];
                $ep->setEstado(0);
                $em->persist($ep);
            }
            $em->flush();
        }
        $productos = trim(trim(trim($request->request->get('ids_productos')), ','));
        $capacidades = trim($request->request->get('capacidades'));
        $idsProductos = explode(',', $productos);
        $capacidades = explode(',', $capacidades);
   
        $contador = 0;
        foreach ($idsProductos as $ids) {
            $capacidadProducto[$ids] = $capacidades[$contador];
            $contador ++;
             $prod=0;
             
                if(count($empresaProducto)>0) {
                    foreach ($empresaProducto as $k => $val) {
                        if ((integer)$ids == $val->getProducto()->getId()) {
                            var_dump($ids,'=',$val->getProducto()->getId());
                            $ep = $em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('id' => $val->getId()));
                            $ep = $ep[0];
                            $ep->setEstado(1);
                            $em->persist($ep);
                            $prod = 1;
                        }
                    }
                }
            if($prod==0){
                
                $hoy = new \DateTime("now");
                $prod= $em->getRepository('WsunBundle:Producto')->find($productos);
                $empPr = new EmpresaProducto();
                $empPr->setEmpresa($empresa);
                $empPr->setProducto($prod);
                $empPr->setCapacidad($capacidadProducto[$ids]);
                $empPr->setCreated($hoy);
                $empPr->setEstado(1);
                
                $em->persist($empPr);
                }
            }
            $em->flush();
            $mensaje = 'Datos Guardados';  
            //$this->session->getFlashBag()->add("status", $mensaje);
           //return $this->redirectToRoute('admin_productos_empresa_index');
            $response = new Response(json_encode(array('error' => 0,'mensaje' => $mensaje)));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

    } catch (\Exception $e) {
            $mensaje = "Error al Guardar los datos.".$e->getMessage().$e->getLine();
    }
        $response = new Response(json_encode(array('error' => 0,'mensaje' => $mensaje)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

}

    public function showProductosAction(Request $request)
    {
        $id=$request->query->get("id");
        $em = $this->getDoctrine()->getManager();
        $empresasP = $em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('empresa'=>$id));
//        $deleteForm = $this->createDeleteForm($empresa);
//
        return $this->render('WsunBundle:empresa:index_empresa_producto.html.twig', array(
            'empresaP' => $empresasP
            //'delete_form' => $deleteForm->createView(),
       ));
    }
    public function DeleteProductosAction(Request $request)
    {
        $id=$request->query->get("id");
        var_dump($id);die;
        
//        $em = $this->getDoctrine()->getManager();
//        $empresasP = $em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('empresa'=>$id));
////        $deleteForm = $this->createDeleteForm($empresa);
////
//        return $this->render('WsunBundle:empresa:index_empresa_producto.html.twig', array(
//            'empresaP' => $empresasP
//            //'delete_form' => $deleteForm->createView(),
//       ));
    }
}
