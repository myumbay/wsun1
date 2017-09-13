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
        $rol=$options[0];
        /*$pedidoCodigo = $em->getRepository('WsunBundle:Pedido')
            ->findOneBy(
                array(),
                array('id' => 'DESC')
            );
        $codigo=$pedidoCodigo->getCodigoPedido();
        $codigo=$codigo+1;*/

        $builder->add('codigoPedido',TextType::class,array('label'=>'Codigo:'),array('class'=>'form-control'));
        $builder->add('fechaCreacion', DateType::class,array('label'=>'Fecha registro:'));
                //->add('idDepartamento')
        if($rol =='ROLE_ACEPTAR_PEDIDO')
        {
        $builder->add('estadoPedido',  CheckboxType::class ,array('label'=>'Activar:','required' => false));
        $builder->add('updatedBy',HiddenType::class);
        }
        $builder->add('totalPedido');   
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\Pedido','rol' 
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
