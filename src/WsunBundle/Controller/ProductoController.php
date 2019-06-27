<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Liip\ImagineBundle\Imagine\Filter\Loader\ResizeFilterLoader;
use WsunBundle\Form\ProductoType;
use Symfony\Component\HttpFoundation\Session\Session;
use WsunBundle\Entity\Parametro;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class ProductoController extends Controller
{
   private $session;
    public function __construct() {
        $this->session=new Session();
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
    */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $productos = $em->getRepository('WsunBundle:Producto')->findAll(array('nombreProducto' => 'ASC')); 
        $limite = $this->container->getParameter('limitePaginacion');
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
                $productos, 
                $request->query->getInt('page', 1),
                $limite
        );
 
        return $this->render('WsunBundle:producto:index.html.twig',
                array('pagination' => $pagination));
        
     
    }

     /**
     * @Security("has_role('ROLE_ADMIN')")
    */
    public function newAction(Request $request)
    {
       
        $producto = new Producto();
        $form = $this->createForm('WsunBundle\Form\ProductoType', $producto);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $iva = $em->getRepository('WsunBundle:Parametro')->findOneByDescripcion('IVA');
        $iva=$iva->getValor();
		
		$repo = $this->getDoctrine()->getRepository('WsunBundle:producto');
		$id = $repo->find(1);
		if ($id == null ){
			$codigoProducto='SUMER01';
		}else {
			$codigoProducto='SUMER0'.$id;
		}
		
        if ($form->isSubmitted() && $form->isValid()) {
           
               if(!$em->getRepository('WsunBundle:Producto')->findByNombreProducto(trim($producto->getNombreProducto())))
                {
                   $em = $this->getDoctrine()->getManager();
                   $em->persist($producto);
                 //  $em->flush();  
                   /* @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                   $file = $producto->getImagen(); 
                   $path = "{$this->get('kernel')->getRootDir()}/../Documentos/Productos/";
                   $producto->setImagen($file->getClientOriginalName());
                   $narchivo = $producto->getId() . '.' . $file->getClientOriginalName();
                   $producto->setImagen($file->getClientOriginalName());    
                   $em->persist($producto);
                   $em->flush(); 
               
                $file->move(realpath($path), $narchivo);
                //$em->getConnection()->commit();
                    return $this->redirectToRoute('admin_producto_show', array('id' => $producto->getId()));
                } else{
                    $mensaje = 'El producto ya esta registrado';
                    $this->session->getFlashBag()->add("status",$mensaje);
                    return $this->redirectToRoute('admin_producto_new');
                }   
        }
		//var_dump($codigoProducto);die;
        return $this->render('WsunBundle:producto:new.html.twig', array(
            'producto' => $producto,'iva'=>$iva,'codigoProducto'=>$codigoProducto,
            'form' => $form->createView(),
        ));
    }
  public function subcategoriaAction(Request $request)
    {
      $id=$request->request->get('id');
    
        /* @var $qb \Doctrine\ORM\QueryBuilder */
            $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
            $qb->from('WsunBundle:Categoria', 'cat');
            $qb->select('cat.id, cat.nombreCat');
            $qb->andWhere('cat.padreId = :id');
            $qb->setParameter('id', $id);
            $qb->addOrderBy('cat.nombreCat', 'ASC');
            $subcategoria= $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            $response = new Response(json_encode(array('data' => $subcategoria)));
            $response->headers->set('Content-Type', 'application/json');
            return $response; 
    }    
    public function showAction(Request $request,Producto $producto)
    {
        
        $img= $producto->getId().'.'.$producto->getImagen();  
        $root = $this->get('kernel')->getRootDir();
        $url= '../Documentos/Productos/'.$img;
        $em = $this->getDoctrine()->getManager();
        $iva = $em->getRepository('WsunBundle:Parametro')->findOneByDescripcion('IVA');
        $iva=$iva->getValor();
        $deleteForm = $this->createDeleteForm($producto);
        return $this->render('WsunBundle:producto:show.html.twig', array(
            'url'=>$url,
            //'medidas'=>$medidas,
            'producto' => $producto,'iva'=>$iva,
            'delete_form' => $deleteForm->createView(),
        ));
    }
public function redimensionar($src, $ancho_forzado){
    if (file_exists($src)) {
      list($width, $height, $type, $attr)= getimagesize($src);
      if ($ancho_forzado > $width) {
         $max_width = $width;
      } else {
         $max_width = $ancho_forzado;
      }
      $proporcion = $width / $max_width;
      if ($proporcion == 0) {
         return -1;
      }
      $height_dyn = $height / $proporcion;
   } else {
      return -1;
   }
   return array($max_width, $height_dyn);
}
     /**
     * @Security("has_role('ROLE_ADMIN')")
    */
    public function editAction(Request $request, Producto $producto)
    {        
        $deleteForm = $this->createDeleteForm($producto);
		$idPadre=$producto->getCategoria()->getPadreId();
        $editForm = $this->createForm('WsunBundle\Form\ProductoType', $producto);
        $editForm->handleRequest($request);
        //$idPadre=$producto->getCategoria()->getPadreId();
        $img = $producto->getId().'.'.$producto->getImagen();
        $url= '../Documentos/Productos/'.$img;
        $em = $this->getDoctrine()->getManager();
        $iva = $em->getRepository('WsunBundle:Parametro')->findOneByDescripcion('IVA');
        $iva=$iva->getValor();
		//$producto->getCategoria());
        if ($editForm->isSubmitted()) {
            
            
            /* @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                    $file = $producto->getImagen(); 
                    if($file){
                    $path = "{$this->get('kernel')->getRootDir()}/../Documentos/Productos/";
                    $producto->setImagen($file->getClientOriginalName());          
                   
                    $narchivo = $producto->getId() . '.' . $file->getClientOriginalName();                    
                    $file->move(realpath($path), $narchivo); 
//                    $t1=$this->getParameter('imagesize2');//$request->get('imagesixe2');
//                    $t2=$this->getParameter('imagesize1');
//                    $image = new \Imagick('../Documentos/Productos/'.$narchivo );
//                                           $image->cropthumbnailimage($t1, $t1);
//                        $image->writeimage( '../Documentos/Productos/'.$narchivo );
//                        $image->cropthumbnailimage($t2, $t2);
//                        $image->writeimage( '../Documentos/Productos/Products/'.$narchivo );
                    }
                    $em->persist($producto);
                    $em->flush();
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('admin_producto_show', array('id' => $producto->getId(),'idPadre'=>$idPadre));
        }

        return $this->render('WsunBundle:producto:edit.html.twig', array(
        'url'=>$url,
            'producto' => $producto,'iva'=>$iva,'idPadre'=>$idPadre,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a producto entity.
     *
     */
    public function deleteAction(Request $request, Producto $producto)
    {
        $form = $this->createDeleteForm($producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($producto);
            $em->flush();
        }

        return $this->redirectToRoute('admin_producto_index');
    }

    /**
     * Creates a form to delete a producto entity.
     *
     * @param Producto $producto The producto entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Producto $producto)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_producto_delete', array('id' => $producto->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
     public function buscarAction(Request $request) {
       if ($valor = trim($request->get('valor'))) {
            $request->getSession()->set('VALOR_BUSQUEDA', $valor);
            $request->getSession()->set('DIRECCION_BUSQUEDA', $request->server->get('REQUEST_URI'));
       } else {
           $request->getSession()->set('VALOR_BUSQUEDA', '');
            $request->getSession()->set('DIRECCION_BUSQUEDA', '');
            //return $this->redirect($this->generateUrl('sercop_comun_newhomepage'));
        }
       
        $valorOrig = $valor;
        $valor = "%{$valor}%";
        if ($pag = $request->get('pag', 0)) {
            $request->getSession()->set('PAGE', $request->get('pag'));
        } else {
            $pag = $request->getSession()->get('PAGE', 1);
        }
        
        $max = 10; //$this->container->getParameter('maxPorPag');
        $em = $this->getDoctrine()->getManager();
        /* @var $qb \Doctrine\ORM\QueryBuilder */
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->from('WsunBundle:Producto', 'p');
        $qb->select('p');
        $qb->orwhere($qb->expr()->like($qb->expr()->lower('p.nombreProducto'), $qb->expr()->lower(':valor')));
        $qb->andwhere('p.estado = :estado');
        $qb->setParameter('valor', $valor);
        $qb->setParameter('estado', '1');
        $productos = $qb->getQuery()->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        foreach ($productos as $producto) {
            $ids[] = $producto['id'];
            
        }
       
        //$path = "{$this->get('kernel')->getRootDir()}/../Documentos/Productos/";
      return $this->render('WsunBundle:producto:lista.html.twig', array('buscar' => $valorOrig, 'producto'=> $productos, 'listas' => array(), 'filtros' => array()));
    }
    
    public function carruselAction($tipo, $id = 0) {
        switch ($tipo) {
            case 1: // Mas visto
                /* @var $qb \Doctrine\ORM\QueryBuilder */
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                $qb->from('SercopComunBundle:Producto', 'p');
                $qb->select("p.nombreProducto, p.id");
                $qb->leftJoin('p.categoria', 'c');

                
                $qb->andwhere('p.estado = :estado');
                $qb->setParameter('estado', '1');
                $productos = $qb->getQuery()->execute();           

                                              
                $ids = array();
                foreach ($productos as $producto) {
                    $ids[] = $producto['id'];
                }                
                $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
                
                //$imagenes = $this->buscarImagenes($ids);
                
                return $this->render('WsunBundle:Producto:carousel.html.twig', array('titulo' => '<span>Productos</span> ',
                            'numero_objetos' => 6,
                            'numero_visibles' => 3,
                            'id' => 1,
                            'automatico' => true,
                            'clases' => "panel-verde-oscuro mas-vistos",
                            'productos' => $productos,
                            //'imagenes' => $imagenes,
                            //'atts' => $atts,
                                )
                );
              
                break;
        }
    }
}
