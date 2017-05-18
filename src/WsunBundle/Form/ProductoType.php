<?php

namespace WsunBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Validator\Constraints\NotBlank;
class ProductoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       
//        $validador = function(FormEvent $event) {
//            
//            $form = $event->getForm();
//            $entity = $form->getData();
            //var_dump($entity,$form);die;
            // Recogemos el fichero
           // $file=$form['imagen']->getData();
            /* @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
//            $file = $entity->getImagen();
//         var_dump($file);die;            
// Sacamos la extensión del fichero
            //$ext=$file->guessExtension();

            // Le ponemos un nombre al fichero
            //$file_name=time().".".$ext;

            // Guardamos el fichero en el directorio uploads que estará en el directorio /web del framework
            //$file->move("uploads", $file_name);

            // Establecemos el nombre de fichero en el atributo de la entidad
            //$miEntidad->setImage($file_name);
        
//        };
        $builder
                ->add('nombreProducto',  TextType::class,array('label'=>'Nombre Producto:'))
               // ->add('imagen' ,  TextType::class,array('label'=>'Imagen:'))//, array("attr"=>array("class"=>"form form-control")))
                ->add('imagen', FileType::class,array('label' => 'Imagen:','data_class' => null,'attr' =>array('class' => 'form-control')))
                
                ->add('codigoProducto', TextType::class,array('label'=>'Código Producto:'))
                ->add('precioProducto',TextType::class,array('label'=>'Precio:'))
                ->add('observacion',TextType::class,array('label'=>'Descripcion:'))
                ->add('estado',  CheckboxType::class,array('label'=>'Estado:'))
                ->add('categoria', \Symfony\Bridge\Doctrine\Form\Type\EntityType::class,
                        array('class' => 'WsunBundle\Entity\Categoria'));
//                $builder->addEventListener(FormEvents::PRE_SUBMIT, $validador);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\Producto'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wsunbundle_producto';
    }


}
