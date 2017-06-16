<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Liip\ImagineBundle\Imagine\Filter\Loader\ResizeFilterLoader;
use WsunBundle\Form\ProductoType;
use Symfony\Component\HttpFoundation\Session\Session;
/**
 * Producto controller.
 *
 */
class ProductoController extends Controller
{
   private $session;
    public function __construct() {
        $this->session=new Session();
    }
    
    /**
     * Lists all producto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        //$productos = $em->getRepository('WsunBundle:Producto')->findBy( array('estado' => '1'), array('nombreProducto' => 'ASC'));
        $productos = $em->getRepository('WsunBundle:Producto')->findAll(array('nombreProducto' => 'ASC'));        
        return $this->render('WsunBundle:producto:index.html.twig', array(
            'productos' => $productos,
        ));
    }

    /**
     * Creates a new producto entity.
     *
     */
    public function newAction(Request $request)
    {
        $producto = new Producto();
        $form = $this->createForm('WsunBundle\Form\ProductoType', $producto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /* @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                if(!$em->getRepository('WsunBundle:Producto')->findByNombreProducto(trim($producto->getNombreProducto())))
                {
                    $file = $producto->getImagen();                    
                    $path = "{$this->get('kernel')->getRootDir()}/../Documentos/Productos/";
                    $producto->setImagen($file->getClientOriginalName());          
                    $em->persist($producto);
                    $narchivo = $producto->getId() . '.' . $file->getClientOriginalName(); 
                    //$image = new \Imagick('../Documentos/Productos/pastel.jpg' );
                    $file->move(realpath($path), $narchivo);  
                    //$image = new \Imagick('../Documentos/Productos/'.$narchivo );
                    //$image->cropthumbnailimage(300, 300);
                    //Guarda el archivo mas corto
                    //$image->writeimage( '../Documentos/Productos/imagen_thumb.png' );
                    //$image->writeimage( '../Documentos/Productos/'.$narchivo );
                    //$image->cropthumbnailimage(150, 150);
                    //$image->writeimage( '../Documentos/Productos/Products/'.$narchivo );
                    $em->flush();
                    return $this->redirectToRoute('admin_producto_show', array('id' => $producto->getId()));
                } else{
                    $mensaje = 'El producto ya esta registrado';
                    $this->session->getFlashBag()->add("status",$mensaje);
                    return $this->redirectToRoute('admin_producto_new');
                }   
        }

        return $this->render('WsunBundle:producto:new.html.twig', array(
            'producto' => $producto,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a producto entity.
     *
     */
    public function showAction(Request $request,Producto $producto)
    {

        $img= $producto->getId().'.'.$producto->getImagen();  
        $root = $this->get('kernel')->getRootDir();
        $url= '../Documentos/Productos/'.$img;
        //$medidas=array();
        //$size=$this->getParameter('dimension_imagen1');
       // $medidas=$this->redimensionar($url,$size);
        
        $deleteForm = $this->createDeleteForm($producto);
        return $this->render('WsunBundle:producto:show.html.twig', array(
            'url'=>$url,
            //'medidas'=>$medidas,
            'producto' => $producto,
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
     * Displays a form to edit an existing producto entity.
     *
     */
    public function editAction(Request $request, Producto $producto)
    {        
        $deleteForm = $this->createDeleteForm($producto);
        $editForm = $this->createForm('WsunBundle\Form\ProductoType', $producto);
        $editForm->handleRequest($request);
        $img = $producto->getId().'.'.$producto->getImagen();
        $url= '../Documentos/Productos/'.$img;
    
        if ($editForm->isSubmitted()) {
            
            $em = $this->getDoctrine()->getManager();
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
                    return $this->redirectToRoute('admin_producto_show', array('id' => $producto->getId()));
        }

        return $this->render('WsunBundle:producto:edit.html.twig', array(
        'url'=>$url,
            'producto' => $producto,
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
      return $this->render('WsunBundle:producto:lista.html.twig', array('buscar' => $valorOrig, 'producto'=> $productos, 'listas' => array()));
    }
}
