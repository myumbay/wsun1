<?php
namespace WsunBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\DateTime;

class AdminController extends Controller
{
    private $session;
    public function __construct() {
        $this->session=new Session();
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function ReportesAction(Request $request) {
        return $this->render('WsunBundle:Admin:reportes.html.twig');
    }
    /**
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function EmpresaAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $provinciasTmp = $em->getRepository('WsunBundle:Ubicacion')->findAll();
        $provincias = array();
        foreach ($provinciasTmp as $provincia) {
            $provincias[$provincia->getDetalle()] = $provincia->getDetalle();
        }
        //$hoy=new \DateTime('Y-m-d');
        $time = new \DateTime();
        $hoy=$time->format('Y-m-d');
        $form = $this->createFormBuilder(array(), array('attr' => array ( 'id' => 'frmFiltros'), 'method' => 'post'))
                //->add('nombre', null, array('attr'=>array('class'=>'typeahead empresa form-control input-sm')))
                //->add('provincia', ChoiceType::class , array('choices' => $provincias))
                ->add('desde', TextType::class,array('data' => $hoy,'attr' => array('class' => 'form_datetime','readonly' => true)))
                ->add('hasta', TextType::class,array('data' => $hoy,'attr' => array('class' => 'form_datetime','readonly' => true)))
                ->add('idEmpresa', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
                ->getForm();
        $em = $this->getDoctrine()->getManager();
        /* @var $qb Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder(); 
        $qb->from('WsunBundle:Empresa', 'e');
        $qb->select('e.id,e.ruc,e.nombreEmp');
        $empresa = $qb->getQuery()->getResult();
        return $this->render('WsunBundle:Admin:empresa.html.twig', array('empresa' => $empresa, 'form' => $form->createView()));
      
    }
    public function consultaDepartamentoAction(Request $request) {
          $id=$request->request->get('id');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->from('WsunBundle:Departamento', 'dpto');
            $qb->select('dpto.id, dpto.nombreDep');
            $qb->andWhere('dpto.idEmpresa = :id');
            $qb->setParameter('id', $id);
            $qb->addOrderBy('dpto.nombreDep', 'ASC');
            $departamento = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $response = new Response(json_encode(array('data' => $departamento)));
            $response->headers->set('Content-Type', 'application/json');
            return $response; 
    }
    public function consultaProductosAction(Request $request) {
        $id=$request->get('id');
        $empresa=$request->get('empresa_id');
        $desde=$request->get('desde');
        $hasta=$request->get('hasta');
        $em = $this->getDoctrine()->getManager();
        $iva = $em->getRepository('WsunBundle:Parametro')->findOneByDescripcion('IVA');
        $iva=$iva->getValor();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:DetallePedido', 'dp');
        $qb->select('prod.id,prod.nombreProducto,dpt.nombreDep,prod.iva,sum(dp.cantidad) total');
        $qb->innerJoin('dp.idProducto', 'emProd');
        $qb->innerJoin('emProd.producto', 'prod');
        $qb->innerJoin('dp.idPedido', 'ped');
        $qb->innerJoin('ped.idUsuario', 'u');
        $qb->innerJoin('u.departamento', 'dpt');
        $qb->innerJoin('dpt.idEmpresa', 'e');
        $qb->andWhere('e.id = :empresa');
        $qb->setParameter('empresa', $empresa);
        if($id>0) {
            $qb->andWhere('dpt.id = :dep');
            $qb->setParameter('dep', $id);
        }
        $qb->andWhere('ped.fechaCreacion >= :desde');
        $qb->setParameter('desde', $desde);
        $qb->andWhere('ped.fechaCreacion <= :hasta');
        $qb->setParameter('hasta', $hasta);
        $qb->addGroupBy('prod.id,prod.nombreProducto,dpt.nombreDep, prod.iva');
        $qb->addOrderBy('prod.nombreProducto', 'ASC');

        $productos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
            $productos,
            $request->query->getInt('page', 1),
            $limite
        );

        return $this->render('WsunBundle:Admin:lista_productos.html.twig',
            array('pagination' => $pagination,'iva'=>$iva));
   }
    public function ConsultaAjaxAction(Request $request) {
         $query = $request->get('query');
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:Empresa', 'e');
        $qb->orWhere(
                $qb->expr()->like($qb->expr()->lower('e.nombreEmp'), $qb->expr()->lower(":valor"))
        );
        $qb->select('e.id,e.nombreEmp, e.ruc');
        //$qb->groupBy('prov.id,prov.razonSocial, prov.ruc');
        $qb->orWhere(
                $qb->expr()->like($qb->expr()->lower('e.ruc'), $qb->expr()->lower(":valor"))
        );

        $qb->setParameter('valor', "%{$query}%");
        $qb->setMaxResults(20);
        $rows = $qb->getQuery()->execute();
        $results = array();
        foreach ($rows as $row) {
            $results[$row['ruc']] = array('id'=> "{$row['id']}",'value' => "{$row['nombreEmp']} ({$row['ruc']})", 'data' => $row['ruc']);
        }
        $response = new Response(json_encode(array('query' => $query, 'suggestions' => $results)));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
               
    }
}
