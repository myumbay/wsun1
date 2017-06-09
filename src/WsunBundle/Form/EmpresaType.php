<?php

namespace WsunBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
class EmpresaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('ruc',  TextType::class,array('label'=>'Ruc:'))
                ->add('nombreEmp' ,  TextType::class,array('label'=>'Nombre Empresa:'))//, array("attr"=>array("class"=>"form form-control")))
                ->add('telefonoEmp', TextType::class,array('label'=>'Telf. Empresa:'))
                ->add('direccionEmp', TextType::class,array('label'=>'Dirección Empresa:'))
                ->add('email', TextType::class,array('label'=>'E-mail Empresa:'))
                ->add('ordenCompra', TextType::class,array('label'=>'N. Orden:'))
                ->add('ubicacion')
                ->add('credito',  CheckboxType::class,array('label'=>'Crédito:'))
                ->add('limiteOrden', TextType::class,array('label'=>'limite:'))
                ->add('idubicacion');
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\Empresa'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wsunbundle_empresa';
    }


}
