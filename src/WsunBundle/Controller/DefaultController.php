<?php

namespace WsunBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
    public function productsAction(Request $request)
    {
        $id=$request->get('id');
        $em = $this->getDoctrine()->getManager();
        $in = $em->createQueryBuilder()
            ->select('ep')
            ->from('WsunBundle:EmpresaProducto','ep')
            ->where('ep.empresa=:slug')
            ->andWhere('ep.estado=:estado')
            ->setParameter('slug', $id)
            ->setParameter('estado', '1');
        $pem=$in->getQuery()->getResult();

        for($i=0;$i< count($pem);$i++)
            {
                $idprod[]=$pem[$i]->getProducto()->getId();
            }
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->from('WsunBundle:Producto', 'p');
        //$qb->innerJoin('p.categoria', 'c');
        $qb->select('p')->distinct();
        //$qb->andWhere($qb->expr()->notIn('p.id',$idprod));
        $qb->andWhere('p.estado = :estado');
        $qb->setParameter('estado', '1');
        $qb->addOrderBy('p.categoria', 'ASC');
        $qb->addOrderBy('p.nombreProducto', 'ASC');
        $p = $qb->getQuery()->getResult();
        for($i=0;$i< count( $p);$i++)
            {
                $idcat[]=$p[$i]->getCategoria()->getId();
            }
        //$categoria=$em->getRepository('WsunBundle:Categoria')->findById($idcat); 
        $categoria = $em->getRepository('WsunBundle:Categoria')
          ->findBy(
             array('id'=> $idcat), 
             array('nombreCat' => 'ASC')
           );
        //return $this->render('WsunBundle:Default:respuesta_buscar_productos_convenio.html.twig', array('productos' => $pep, 'convenio'=>$convenio))
        return $this->render('WsunBundle:Default:products.html.twig',array('productos' => $p,'categoria'=>$categoria,'idprod'=>$idprod));
    }
    public function addProductsAction(Request $request)
    {
//        $em = $this->getDoctrine()->getManager();
//        $in = $em->createQueryBuilder()
//            ->select('ep')
//            ->from('WsunBundle:EmpresaProducto','ep')
//            ->where('ep.empresa=:slug')
//            ->setParameter('slug', 1);
//        $pem=$in->getQuery()->getResult();
//
//        for($i=0;$i< count($pem);$i++)
//            {
//                $idprod[]=$pem[$i]->getProducto()->getId();
//            }
//        /* @var $qb \Doctrine\ORM\QueryBuilder */
//        $qb = $em->createQueryBuilder();
//        $qb->from('WsunBundle:Producto', 'p');
//        $qb->innerJoin('p.categoria', 'c');
//        $qb->select('p')->distinct();
//       
//        //if($id>0){
//        $qb->andWhere($qb->expr()->notIn('p.id',$idprod));
//        //}
//        $qb->andWhere('p.estado = :estado');
//        $qb->setParameter('estado', '1');
//        $qb->addOrderBy('p.nombreProducto', 'ASC');
//        $p = $qb->getQuery()->getResult();
        return $this->render('WsunBundle:Default:addProducts.html.twig');
        //return $this->render('WsunBundle:Default:addProducts.html.twig',array('productos' => $p));
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
