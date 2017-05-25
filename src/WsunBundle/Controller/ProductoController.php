<?php

namespace WsunBundle\Controller;

use WsunBundle\Entity\Producto;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Liip\ImagineBundle\Imagine\Filter\Loader\ResizeFilterLoader;
use WsunBundle\Form\ProductoType;
/**
 * Producto controller.
 *
 */
class ProductoController extends Controller
{
    /**
     * Lists all producto entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $productos = $em->getRepository('WsunBundle:Producto')->findAll();

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
                    $file = $producto->getImagen();                    
                    $path = "{$this->get('kernel')->getRootDir()}/../Documentos/Productos/";
                    $producto->setImagen($file->getClientOriginalName());          
                    $em->persist($producto);
                    $em->flush();
                    $narchivo = $producto->getId() . '.' . $file->getClientOriginalName();                    
                    $file->move(realpath($path), $narchivo);  

            return $this->redirectToRoute('admin_producto_show', array('id' => $producto->getId()));
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
        $medidas=array();
        
        //$img1=$this->get('kernel')->getRootDir().$url;
        //$image= new \Liip\ImagineBundle\Form\Type\ImageType($img1);
        //var_dump($image);die;
        //$image->readImage("image.jpg");
        $size=$this->getParameter('dimension_imagen1');
        $medidas=$this->redimensionar($url,$size);
        
        $deleteForm = $this->createDeleteForm($producto);
        return $this->render('WsunBundle:producto:show.html.twig', array(
            'url'=>$url,
            'medidas'=>$medidas,
            'producto' => $producto,
            'delete_form' => $deleteForm->createView(),
        ));
    }
public function redimensionar($src, $ancho_forzado){
   //var_dump($src);die;
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
        $img=$producto->getImagen();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            /* @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                    $file = $producto->getImagen();                    
                    $path = "{$this->get('kernel')->getRootDir()}/../Documentos/Productos/";
                    $producto->setImagen($file->getClientOriginalName());          
                    $em->persist($producto);
                    //$em->flush();
                    
            
            $this->getDoctrine()->getManager()->flush();
            $narchivo = $producto->getId() . '.' . $file->getClientOriginalName();                    
            $file->move(realpath($path), $narchivo); 

            return $this->redirectToRoute('admin_producto_edit', array('id' => $producto->getId()));
        }

        return $this->render('WsunBundle:producto:edit.html.twig', array(
        'img'=>$img,
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
}
