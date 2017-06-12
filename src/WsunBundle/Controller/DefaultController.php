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
      public function addProductsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        $prod = $em->getRepository('WsunComunBundle:Productos')->findAll();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->from('WsunBundle:Producto', 'p');
        $qb->select('p')->distinct();
        $qb->andWhere('p.estado = :estado');
        $qb->setParameter('estado', '1');
        $qb->addOrderBy('p.nombreProducto', 'ASC');
        $p = $qb->getQuery()->getResult();
        
        //return $this->render('WsunBundle:Default:respuesta_buscar_productos_convenio.html.twig', array('productos' => $pep, 'convenio'=>$convenio))
         
        return $this->render('WsunBundle:Default:addProducts.html.twig',array('productos' => $p,));
    }
  
}
