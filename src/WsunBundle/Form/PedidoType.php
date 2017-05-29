<?php

namespace WsunBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class PedidoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('codigoPedido',TextType::class,array('label'=>'Codigo:'),array('class'=>'form-control'))
                ->add('fechaCreacion', DateType::class,array('label'=>'Fecha registro:'))
                //->add('idDepartamento')
                ->add('estadoPedido',CheckboxType::class,array('label'=>'Estado:'))
                ->add('iva',HiddenType::class)
                ->add('totalPedido');
                
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\Pedido'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wsunbundle_pedido';
    }


}
