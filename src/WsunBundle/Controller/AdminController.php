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
        $form = $this->createFormBuilder(array(), array('attr' => array ( 'id' => 'frmFiltros'), 'method' => 'post'))
                ->add('nombre', null, array('attr'=>array('class'=>'typeahead empresa form-control input-sm')))
                ->add('provincia', ChoiceType::class , array('choices' => $provincias))
                ->add('desde', DateType::class)
                ->add('hasta', DateType::class)
                ->add('idEmpresa', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
                ->getForm();
        
        $em = $this->getDoctrine()->getManager();
        $form->handleRequest($request);
        $ordenes = array();
        $totales = array();
     
        if ($form->isValid()) {
            $filtros = $form->getData();
            $maxDate = clone $filtros['desde'];            
            $maxDate->modify('+6 months');                                              
            if ($filtros['hasta']>$maxDate || $filtros['hasta']<$filtros['desde'])
            {
                $mensaje = 'Las Fechas no deben sobrepasar los 6 meses  a partir de la fecha Desde !!';
                $this->session->getFlashBag()->add("status",$mensaje);
                return $this->redirectToRoute('wsun_admin_reportes_productos_empresa');
            
            } 
           
//            $totalregistros=$this->getTotalEntidad($filtros['provincia'],$filtros['desde'],$filtros['hasta']);
//            $page=$request->get('pagina');
//            
//            if($totalregistros>0)
//            {    
//            $arrayPage=$this->fnPage($page, $totalregistros);
//            $porpagina=$arrayPage[3];
          $Empresa = $em->getRepository('WsunBundle:Empresa')->findOneByRuc($filtros['idEmpresa']);  
         
          /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $em->createQueryBuilder();
            $qb->from('WsunBundle:Pedido', 'ped');
            //$qb->from('WsunBundle:Usuarios', 'u');
            $qb->select('ped,u,dpt,e');
            //$qb->addSelect("SUM( dO.cantidad * (dO.subtotal - dO.descuento)* (dO.iva + 100)/100 ) as total,dO.plazoServicio as plazo,(case when (dO.plazoServicio>0 and dO.tipoPlazo is not null) then dO.tipoPlazo else '1' end) as tipoPlazo");
            $qb->innerJoin('ped.idUsuario', 'u');
            $qb->innerJoin('u.departamento', 'dpt');
            $qb->innerjoin('dpt.idEmpresa', 'e');
            $qb->andWhere('dpt.idEmpresa = :empresa');
            $qb->setParameter('empresa', $Empresa->getId());
            $ordenes=$qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);; 
 
            }
   
            //return $this->render('WsunBundle:Admin:empresa.html.twig', array('ordenes' => $ordenes, 'form' => $form->createView(),'page'=>$request->get('pagina')));
            return $this->render('WsunBundle:Admin:empresa.html.twig', array('ordenes' => $ordenes, 'form' => $form->createView()));
      
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
