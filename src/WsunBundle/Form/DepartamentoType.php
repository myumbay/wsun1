<?php

namespace WsunBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
class DepartamentoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('nombreDep',  TextType::class,array('label'=>'Nombre Departamento:'))
                ->add('responsable' ,  TextType::class,array('label'=>'Nombre Responsable:'))//, array("attr"=>array("class"=>"form form-control")))
                ->add('telefono', TextType::class,array('label'=>'Telf.:'))
                ->add('valor', TextType::class,array('label'=>'Valor:'))
                ->add('idEmpresa');
                ;
        
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\Departamento'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wsunbundle_departamento';
    }


}
