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
      
        /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->from('WsunBundle:DetallePedido', 'emProd');
            $qb->select('prod.id ,prod.nombreProducto,prod.Iva,sum(empro.cantidad)');
            $qb->innerJoin('dped.idProducto', 'emProd');//empresa producto
            $qb->innerJoin('emProd.producto', 'prod');
            $qb->innerJoin('dped.idPedido', 'ped');
            $qb->innerJoin('ped.idUsuario', 'u');
            $qb->innerJoin('u.departamento', 'dpt');
            $qb->innerjoin('dpt.idEmpresa', 'e');
            
            
//            $qb->from('WsunBundle:Empresa', 'e');
//            $qb->select('prod.id ,prod.nombreProducto,prod.Iva,sum(empro.cantidad)');
//            $qb->innerJoin('e.departamento', 'dpt');
//            $qb->innerJoin('dpt.usuarios', 'u');
//            $qb->innerJoin('u.pedido', 'ped');
//            $qb->innerJoin('ped.detallePedido', 'dped');
//            $qb->innerJoin('dped.empresaProducto', 'empro');
//            $qb->innerJoin('empro.producto', 'prod');
            
            $qb->andWhere('e.id = :empresa');
            $qb->andWhere('dep.id = :dep');
            $qb->setParameter('empresa', $empresa);
          //  $qb->setParameter('estado', '1');
            $qb->setParameter('dep', $id);
            $qb->addOrderBy('prod.nombreProducto', 'ASC');
            $qb->addGroupBy('empro.cantidad');
            $productos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
           
        return $this->render('WsunBundle:Admin:lista_productos.html.twig', array('productos' =>$productos));
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
