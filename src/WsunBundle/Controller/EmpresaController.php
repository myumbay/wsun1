<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Empresa;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WsunBundle\Entity\EmpresaProducto;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Empresa controller.
 *
 */
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
     * Creates a new empresa entity.
     *
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
     * Displays a form to edit an existing empresa entity.
     *
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
     * Deletes a empresa entity.
     *
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
     * Creates a form to delete a empresa entity.
     *
     * @param Empresa $empresa The empresa entity
     *
     * @return \Symfony\Component\Form\Form The form
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
    public function EmpresaGuardarAction(Request $request)
    {
       //var_dump($_POST,$request->get('productos'));die;
       $idempresa=$request->get('empresaId');
        try{
            if($empresa='' )
                {      
                $mensaje = 'No existe la empresa seleccionada';
                $this->session->getFlashBag()->add("status",$mensaje);
                return $this->redirectToRoute('admin_productos_empresa_index');

                }else{
                    
                    
                }
        $em = $this->getDoctrine()->getManager();
        $empresa=$em->getRepository('WsunBundle:Empresa')->find($idempresa);
        //$empresaProducto=$em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('empresa'=>$idempresa));
        foreach ($request->get('productos') as $key => $valor){
             $prod=0;   
            //var_dump($empresaProducto);die;
                $hoy = new \DateTime("now");
                $prod= $em->getRepository('WsunBundle:Producto')->find($valor);
                $empPr = new EmpresaProducto();
                $empPr->setEmpresa($empresa);
                $empPr->setProducto($prod);
                //$empPr->setCapacidad($capacidades[$i]);
                $empPr->setCreated($hoy);
                $em->persist($empPr);
           
        }
        $em->flush();
        $mensaje = 'Datos Guardados';
        $this->session->getFlashBag()->add("status",$mensaje);
        return $this->redirectToRoute('admin_productos_empresa_index');
    } catch (\Exception $e) {
            $mensaje = "Error al Guardar los datos.";
    }    
       
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
