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
    public function addProductsAction(Request $request,$id)
    {
        $em = $this->getDoctrine()->getManager();
        $in = $em->createQueryBuilder()
            ->select('ep')
            ->from('WsunBundle:EmpresaProducto','ep')
            ->where('ep.empresa=:slug')
            ->setParameter('slug', 1);    
        $pem=$in->getQuery()->getResult();
        for($i=0;$i< count($pem);$i++)
            {
                $idprod[]=$pem[$i]->getProducto()->getId();
            }
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $em->createQueryBuilder();
        $qb->from('WsunBundle:Producto', 'p');
        $qb->select('p')->distinct();
        
        if($id>0){
        $qb->andWhere($qb->expr()->notIn('p.id',$idprod));
        }
        $qb->andWhere('p.estado = :estado');
        $qb->setParameter('estado', '1');
        $qb->addOrderBy('p.nombreProducto', 'ASC');
        $p = $qb->getQuery()->getResult();
        //return $this->render('WsunBundle:Default:respuesta_buscar_productos_convenio.html.twig', array('productos' => $pep, 'convenio'=>$convenio))
        return $this->render('WsunBundle:Default:addProducts.html.twig',array('productos' => $p,));
    }
  
}
