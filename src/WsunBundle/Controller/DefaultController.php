<?php

namespace WsunBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Query\Expr\Join;
class DefaultController extends Controller
{
    private $session;
    public function __construct() {
        $this->session=new Session();
    }
    public function indexAction()
    {
       return $this->render('WsunBundle:Default:index.html.twig');
        //return $this->render('WsunBundle:detallepedido:mail.html.twig');
    }
     public function contactosAction(Request $request)
    {
        $form = $this->createFormBuilder(array(), array('attr' => array ( 'id' => 'frmFiltros'), 'method' => 'post'))
                //->add('provincia', 'choice', array('choices' => $provincias, 'empty_value' => ' '))
                ->add('email', EmailType::class, array('label' => 'Ingrese el correo '))
                ->add('asunto',TextType::class, array('label' => 'Asunto'))
                ->add('detalle',TextareaType::class, array('label' => 'Mensaje'))
                ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $filtros = $form->getData();
            $email=$filtros['email'];
            $asunto=$filtros['asunto'];
            $detalle=$filtros['detalle'];
            
            /* @var $correo \WsunBundle\ComunBundle\Services\Correo */
            $correoE = $this->get('sistema_de_correos');
            //$correoE->enviarPrueba($email);
            $enviar=$correoE->enviarContacto($email,$asunto,$detalle);
            //return $this->render('WsunBundle:Default:prueba.html.twig');
           
             if($enviar=true )
                {      
                $mensaje = 'El mensaje se ha enviado correctamente';
                $this->session->getFlashBag()->add("status",$mensaje);
                return $this->redirectToRoute('wsun_contactos');
                }else {
                 $mensaje = 'El mensaje no se ha enviado correctamente, existen datos incorrectos!!';
                $this->session->getFlashBag()->add("status",$mensaje);
                return $this->redirectToRoute('wsun_contactos');
            }
   
             
            }
   
        return $this->render('WsunBundle:Default:contactos.html.twig',array('form' => $form->createView()));
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
		/* @var $qb \Doctrine\ORM\QueryBuilder */	
		$qb = $em->createQueryBuilder();
		$qb->from('WsunBundle:Categoria', 'c1');
        $qb->select('c1.id,c1.padreId, c1.nombreCat nc1,c2.id idC2,c2.nombreCat nc2');
        $qb->innerJoin('c1.padre', 'c2');
       // $qb->andWhere('c2.estado = :estado'); // Es estado de Categoria, es decir habilitado si es categoria principal
       // $qb->setParameter('estado', '1');
		$qb->orderBy('nc2,nc1','ASC');
        $categoria = $qb->getQuery()->getResult();
		//var_dump($qb->getQuery()->getSQL());
		//var_dump($categoria);die;
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

  }
    private function crearCadena($n1, $n2, $n3, $n4) {
        $r = $n1;
        $s = '';
        $ns = array($n4, $n3, $n2);
        $ns2 = array();
        foreach ($ns as $n) {
            if ($n) {
                $ns2[] = $n;
            }
        }
        $s = implode(', ', $ns2);
        if ($s) {
            $s = "($s)";
        }
        return "$r $s";
    }
  public function productsListAction(Request $request){
      $em = $this->getDoctrine()->getManager();
      $producto = $em->getRepository('WsunBundle:Producto')
          ->findBy(
             array('estado'=>'1'), 
             array('nombreProducto' => 'ASC')
           ); 
		$query = $request->get('query');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:Categoria', 'c');
        $qb->select('c.nombreCat N1, c.id, cp.nombreCat N2 ,cp2.nombreCat N3 ,cp3.nombreCat N4');
        $qb->andWhere($qb->expr()->isNotNull('c.padreId'));
        $qb->andWhere($qb->expr()->like($qb->expr()->lower('c.nombreCat'), $qb->expr()->lower(":nombre")));
        $qb->setParameter('nombre', "%{$query}%");
        $qb->leftJoin('c.padre', 'cp');
        $qb->leftJoin('cp.padre', 'cp2');
        $qb->leftJoin('cp2.padre', 'cp3');
        $qb->setMaxResults(40);
        $categoria = $qb->getQuery()->execute();
      
       // $em = $this->getDoctrine()->getManager();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
       /* $qb = $em->createQueryBuilder();
        $qb->select('a')->addSelect('b')->from('WsunBundle:Categoria', 'a');
        $qb->andWhere($qb->expr()->isNull('a.padreId'));
        $qb->leftJoin('a.hijos', 'b');
        $qb->andWhere('a.estado = :estado');
        $qb->andWhere('b.estado = :estado');
        $qb->setParameter('estado', '1');
        $qb->addOrderBy('a.nombre','ASC');
        $qb->addOrderBy('b.nombre','ASC');
        $query = $qb->getQuery();
        $rows = $query->execute();*/
      
      /*
      $categoria = $em->getRepository('WsunBundle:Categoria')
          ->findBy(
             array('padreId'=>null),
             array('estado'=>'1'),
             array('nombreCat' => 'ASC')
           );*/
      //var_dump($categoria );die;

      return $this->render('WsunBundle:Default:productsList.html.twig',array('categoria' =>$categoria,'productos'=>$producto));
  }
}
