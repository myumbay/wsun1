<?php

namespace WsunBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
class CategoriaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

            $validador=function (FormEvent $event) {
                $product = $event->getData();
                $form = $event->getForm();

        };

        $builder
                ->add('nombreCat',  TextType::class,array('label'=>'Nombre Categoria:'))
                ->add('padreId' ,  \Symfony\Component\Form\Extension\Core\Type\HiddenType::class)//, array("attr"=>array("class"=>"form form-control")))
                ->add('estado',  CheckboxType::class ,array('label'=>'Activar Categoria Principal:','required' => false,'attr'=>array('Escoger Categoria')))

                 ->add('padre', EntityType::class, array(
                    'class' => 'WsunBundle:Categoria',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->andWhere('c.padreId is NULL')
                            //->andWhere('c.padreId =c.id')
                            ->orderBy('c.nombreCat', 'ASC');
                    },
                ));
                $builder->addEventListener(FormEvents::PRE_SET_DATA,$validador);
               // ->add('padre');

                
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\Categoria'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wsunbundle_categoria';
    }


}
