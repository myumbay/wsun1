<?php

namespace WsunBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use \Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DetallePedidoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
           
        $id_empresa = $options[0];
        //$validador = function(FormEvent $event) {
           // var_dump($event->getData());die;      
       //};
        
        $builder->add('codigo')
                ->add('cantidad')
                ->add('valorUnitario')
                ->add('valorTotal')
                ->add('observaciones',  \Symfony\Component\Form\Extension\Core\Type\TextareaType::class)
                //->add('idProducto',  HiddenType::class);
                ->add('idProducto', EntityType::class, array(
                    'class' => 'WsunBundle:EmpresaProducto',
                    'query_builder' => function(EntityRepository $er) use ($id_empresa) {
                        return $er->createQueryBuilder('ep')
                            ->leftJoin('ep.producto', 'p')
                            ->andWhere('p.estado = :estado')
                            ->andWhere('ep.empresa = :id')
                            ->setParameter('estado', '1')
                            ->setParameter('id', $id_empresa);
                            //->orderBy('c.nombreCat', 'ASC');
                    },'choice_label' => 'producto',  'placeholder' => 'Seleccione..',
                ));
         
               // ->add('idProducto');
        
                //->add('idPedido',  HiddenType::class);
      // $builder->addEventListener(FormEvents::PRE_SUBMIT, $validador);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\DetallePedido','id_empresa'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'detallepedido';
    }


}
