<?php

namespace WsunBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('WsunBundle:Default:index.html.twig');
    }
     public function contactosAction(Request $request)
    {
        
         
        return $this->render('WsunBundle:Default:contactos.html.twig');
    }
    public function entradasAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $dql = "SELECT e FROM WsunBundle:Producto e";
        $query = $em->createQuery($dql);
 
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $query, 
                $request->query->getInt('page', 1),
                5
        );
 
        return $this->render('WsunBundle:Default:listado.html.twig',
                array('pagination' => $pagination));
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
    */
    public function productsAction(Request $request)
    {
        $id=$request->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $categoria = $em->getRepository('WsunBundle:Categoria')
          ->findBy(
             array('padreId'=> null), 
             array('nombreCat' => 'ASC')
           );
        
        return $this->render('WsunBundle:Default:products.html.twig',array('categoria'=>$categoria));
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
    */
    public function addProductsAction(Request $request)
    {
        return $this->render('WsunBundle:Default:addProducts.html.twig');
    }
  public function consultaSubcategoriaAction(Request $request){
      
      $id=$request->get('id');
      $em = $this->getDoctrine()->getManager();
      /* @var $qb \Doctrine\ORM\QueryBuilder */
    $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
    $qb->from('WsunBundle:Categoria', 'cat');
    $qb->select('cat');
    $qb->andWhere('cat.padreId = :id');
    $qb->setParameter('id', $id);
    $qb->addOrderBy('cat.nombreCat', 'ASC');
    $categoria = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
      
//      $categoria = $em->getRepository('WsunBundle:Categoria')
//          ->findBy(
//             array('padreId'=> $id), 
//             array('nombreCat' => 'ASC')
//           );
    // var_dump($categoria);die;
      $response = new Response(json_encode(array('data' => $categoria)));
      $response->headers->set('Content-Type', 'application/json');
      return $response;
      
  }
  public function listaProductosAction(Request $request){
      $id=$request->get('id');
      $empresa=$request->get('empresa');
      $em = $this->getDoctrine()->getManager();
      /* @var $qb \Doctrine\ORM\QueryBuilder */
      $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
      $qb->from('WsunBundle:Producto', 'prod');
      $qb->select('prod');
      $qb->andWhere('prod.categoria = :id');
      $qb->setParameter('id', $id);
      $qb->addOrderBy('prod.nombreProducto', 'ASC');
      $productos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
     // $em = $this->getDoctrine()->getManager();
        $in = $em->createQueryBuilder()
            ->select('ep')
            ->from('WsunBundle:EmpresaProducto','ep')
            ->where('ep.empresa=:slug')
            ->andWhere('ep.estado=:estado')
            ->setParameter('slug', $empresa)
            ->setParameter('estado', '1');
        $pem=$in->getQuery()->getResult();
      return $this->render('WsunBundle:Default:productsConsulta.html.twig', array('productos' =>$productos,'pem'=>$pem));
      
//      $producto = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
//      $response = new Response(json_encode(array('data' => $producto)));
//      $response->headers->set('Content-Type', 'application/json');
//      return $response;
  }
  public function productsListAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $producto = $em->getRepository('WsunBundle:Producto')
          ->findBy(
             array('estado'=>'1'), 
             array('nombreProducto' => 'ASC')
           ); 
      $categoria = $em->getRepository('WsunBundle:Categoria')
          ->findBy(
             array(), 
             array('nombreCat' => 'ASC')
           ); 
     
      return $this->render('WsunBundle:Default:productsList.html.twig',array('categoria' => $categoria,'productos'=>$producto));
  }
}
