<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Empresa;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WsunBundle\Entity\EmpresaProducto;
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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $empresas = $em->getRepository('WsunBundle:Empresa')->findAll();

        return $this->render('WsunBundle:empresa:index.html.twig', array(
            'empresas' => $empresas,
        ));
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
       try{
        $mensaje = "";
        $empresa_id = $request->request->get('empresa_id');
        $idsProductos = $request->request->get('ids_productos');
        $capacidades = trim($request->request->get('capacidades'));
        if ($capacidades == '' || $idsProductos == '') {
                    $response = new Response(json_encode(array('error' => 1, 'mensaje' => 'LOS DATOS PROPORCIONADOS SON INCORRECTOS')));
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }
        
         if ($empresa_id == '') {
                $response = new Response(json_encode(array('error' => 1, 'mensaje' => 'NO EXISTE UNA EMPRESA SELECCIONADA')));
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }
        $idsProductos = explode(',', $idsProductos);
        $capacidades = explode(',', $capacidades);
        $contador = 0;
        foreach ($idsProductos as $ids) {
                    $capacidadProducto[$ids] = $capacidades[$contador];
                    $contador ++;
                }
        $proNoEncontrados = '';
        $productos = 0;
        $em = $this->getDoctrine()->getManager();
        $empresa=$em->getRepository('WsunBundle:Empresa')->find($empresa_id);
     
        if (is_array($idsProductos) && count($idsProductos) > 0) {
            for($i=0;$i<count($idsProductos);$i++)
            {
                $Emproductos = $em->getRepository('WsunBundle:EmpresaProducto')->findBy(array('producto' => $idsProductos[$i],'empresa' =>$empresa_id));
                
                if($Emproductos)
                {
                    $Emproductos = $Emproductos[0];
                    if($Emproductos->getCapacidad()>$capacidades[$i]){
                        $response = new Response(json_encode(array('error' => 0, 'mensaje' => 'No guardados!! El valor '.$capacidades[$i].' no debe ser menor que'. $Emproductos->getCapacidad().'  debido a que el valor ya deben estar repartidos en los diferentes departamentos')));
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }else {
                        $Emproductos->setCapacidad($capacidades[$i]);
                        $em->persist($Emproductos);
                    }
                }else{
                    $hoy = new \DateTime("now");
                    $prod= $em->getRepository('WsunBundle:Producto')->find($idsProductos[$i]);
                    $empPr = new EmpresaProducto();
                    $empPr->setEmpresa($empresa);
                    $empPr->setProducto($prod);
                    $empPr->setCapacidad($capacidades[$i]);
                    $empPr->setCreated($hoy);
                    $em->persist($empPr);
                    }
               $em->flush();
               $mensaje .= '<strong> DATOS GUARDADOS EMPRESA: </strong> ' . $empresa->getNombreEmp() . '<br>';
            }

            }
           
         } catch (\Exception $e) {
            $mensaje = "Error al Guardar los datos.";
            
        }    
        
         
        $response = new Response(json_encode(array('error' => 1,'mensaje' => $mensaje)));
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
