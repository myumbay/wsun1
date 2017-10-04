<?php
namespace WsunBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Doctrine\ORM\Query;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
                ->setAction($this->generateUrl('wsun_admin_reportes_productos_empresa'))
                //->add('nombre', null, array('attr'=>array('class'=>'typeahead empresa form-control input-sm')))
                //->add('provincia', ChoiceType::class , array('choices' => $provincias))
                ->add('desde', TextType::class,array('data' => $hoy,'attr' => array('class' => 'form_datetime','readonly' => true)))
                ->add('hasta', TextType::class,array('data' => $hoy,'attr' => array('class' => 'form_datetime','readonly' => true)))
                ->add('idEmpresa', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
                ->add('idDepartamento', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
                ->add('exportar', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)
                ->getForm();
        $em = $this->getDoctrine()->getManager();
        /* @var $qb Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder(); 
        $qb->from('WsunBundle:Empresa', 'e');
        $qb->select('e.id,e.ruc,e.nombreEmp');
        $empresa = $qb->getQuery()->getResult();
        $form->handleRequest($request);
        if ($form->isValid()) {
        $filtros = $form->getData();
        $id=$filtros['idDepartamento'];//$request->get('id');
        $empresa=$filtros['idEmpresa'];//$request->get('empresa_id');
        $desde=$filtros['desde'];
        $hasta=$filtros['hasta'];
        $em = $this->getDoctrine()->getManager();
        $iva = $em->getRepository('WsunBundle:Parametro')->findOneByDescripcion('IVA');
        $iva=$iva->getValor();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:DetallePedido', 'dp');
        $qb->select('prod.id,prod.nombreProducto,dpt.nombreDep,prod.iva,u.username,sum(dp.cantidad) total');
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
        $qb->addGroupBy('prod.id,prod.nombreProducto,dpt.nombreDep, prod.iva,u.username');
        $qb->addOrderBy('prod.nombreProducto', 'ASC');

        $productos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
            $productos,
            $request->query->getInt('page', 1),
            $limite
        );
        if ($filtros['exportar'] == 'csv') {
                $response = new Response();
                $response->headers->set('Content-Type', "text/csv");
                $response->headers->set('Content-Disposition', 'attachment; filename="reporte.csv"');
                $response->headers->set('Pragma', "public");
                $response->headers->set('Expires', "0");
                $response->headers->set('Content-Transfer-Encoding', "binary");
                $response->prepare($request);
                $response->sendHeaders();
                return $this->render('WsunBundle:Admin:lista_productos.csv.twig', array('pagination' => $pagination,'iva'=>$iva));
                die;
            }else if ($filtros['exportar'] == ''){
                return $this->render('WsunBundle:Admin:lista_productos.html.twig',
                array('pagination' => $pagination,'iva'=>$iva));
            }
        }
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

    public function masVendidoAction(Request $request) {
        $id=$request->get('id');
        $empresa=$request->get('empresa_id');
        $desde=$request->get('desde');
        $hasta=$request->get('hasta');
       
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:DetallePedido', 'dp');
        $qb->select('e.nombreEmp,prod.id,prod.nombreProducto,dpt.nombreDep,sum(dp.cantidad) total');
        $qb->innerJoin('dp.idProducto', 'emProd');
        $qb->innerJoin('emProd.producto', 'prod');
        $qb->innerJoin('dp.idPedido', 'ped');
        $qb->innerJoin('ped.idUsuario', 'u');
        $qb->innerJoin('u.departamento', 'dpt');
        $qb->innerJoin('dpt.idEmpresa', 'e');
        if($empresa>0) {
        $qb->andWhere('e.id = :empresa');
        $qb->setParameter('empresa', $empresa);
        }
        if($id>0) {
            $qb->andWhere('dpt.id = :dep');
            $qb->setParameter('dep', $id);
        }
        $qb->andWhere('ped.fechaCreacion >= :desde');
        $qb->setParameter('desde', $desde);
        $qb->andWhere('ped.fechaCreacion <= :hasta');
        $qb->setParameter('hasta', $hasta);
        $qb->addGroupBy('e.nombreEmp,prod.id,prod.nombreProducto,dpt.nombreDep');
        $qb->addOrderBy('prod.nombreProducto', 'Desc');

        $productos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
            $productos,
            $request->query->getInt('page', 1),
            $limite
        );

        return $this->render('WsunBundle:Admin:mas_vendido.html.twig',
            array('pagination' => $pagination));
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
        $qb->select('prod.id,prod.nombreProducto,dpt.nombreDep,prod.iva,u.username,sum(dp.cantidad) total');
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
        $qb->addGroupBy('prod.id,prod.nombreProducto,dpt.nombreDep, prod.iva,u.username');
        $qb->addOrderBy('prod.nombreProducto', 'ASC');

        $productos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $paginator = $this->get('knp_paginator');
        $limite = $this->container->getParameter('limitePaginacion');
        $pagination = $paginator->paginate(
            $productos,
            $request->query->getInt('page', 1),
            $limite
        );

        if ($request->get('exportar') == 'csv') {
                $response = new Response();
                $response->headers->set('Content-Type', "text/csv");
                $response->headers->set('Content-Disposition', 'attachment; filename="reporte.csv"');
                $response->headers->set('Pragma', "public");
                $response->headers->set('Expires', "0");
                $response->headers->set('Content-Transfer-Encoding', "binary");
                $response->prepare($request);
                $response->sendHeaders();
                return $this->render('WsunBundle:Admin:lista_productos.csv.twig', array('pagination' => $pagination,'iva'=>$iva));
                die;
            }

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
    /*exportar a cvs*/
    public function generateCsvAction(Request $request)
    {
         $id=$request->get('id');
            $empresa=$request->get('empresa_id');
            $desde=$request->get('desde');
            $hasta=$request->get('hasta');
            $response = new StreamedResponse();
            $response->setCallback(function() use ($empresa,$hasta,$desde,$id){
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
                 
                $handle = fopen('php://output', 'w+');
                
                // Add the header of the CSV file
                fputcsv($handle, array('No.', 'DEPARTAMENTO', 'PRODUCTO', 'IVA%','TOTAL'),';');
                // Query data from database
               // $results = $this->connection->query($qb);

                // Add the data queried from database
               foreach ($productos as $prod){
                   if($prod['iva']=='1')
                   {
                       $iva=$iva;
                   }else{
                       $iva=0;
                   }

                   fputcsv(
                       $handle, // The file pointer
                       array($prod['id'], $prod['nombreDep'], $prod['nombreProducto'],$iva, $prod['total']), // The fields
                       ';' // The delimiter
                   );


                   /*fputcsv(
                       $handle, // The file pointer
                       array($prod['id'], $prod['nombreDep'], $prod['nombreProducto'], $iva,$prod['total']), // The fields
                       ';' // The delimiter
                   );*/
               }
               fclose($handle);
            });

            $response->setStatusCode(400);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

            return $response;
          
    }
   public function ListaPedidosAction(Request $request){
       
   } 
}
