<?php

namespace WsunBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UsuariosType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id_empresa=$builder->getData()->getDepartamento()->getIdEmpresa()->getId();
        $builder
                ->add('username',TextType::class,array('label'=>'Username: '))
                ->add('password',TextType::class,array('label'=>'Password: '))
                ->add('ruc',TextType::class,array('label'=>'Ruc: '))
                ->add('correo',TextType::class,array('label'=>'Correo: ','label'=>'Correo: '))
                ->add('empresa', EntityType::class, array('mapped' => false,
                'class' => 'WsunBundle:Empresa',
                'query_builder' => function (\Doctrine\ORM\EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.id', 'ASC');
                },
                'choice_label' => 'nombreEmp',
                ))   
                ->add('departamento', EntityType::class, array(
                    'class' => 'WsunBundle:Departamento',
                    'query_builder' => function(\Doctrine\ORM\EntityRepository $er)use ($id_empresa) {
                        return $er->createQueryBuilder('c')
                                
                            ->andWhere('c.idEmpresa = :empresa')
                            ->setParameter('empresa', $id_empresa)    
                            //->andWhere('c.padreId =c.id')
                            ->orderBy('c.nombreDep', 'ASC');
                    },
                ))
                 ->add('user_roles', EntityType::class, array(
                'class'=> 'WsunBundle:Role',
                'multiple' => true, // Allow multiple selection
                'choice_label'=>'detalle')
                    );             
                
               // ->add('user_roles');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'WsunBundle\Entity\Usuarios'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'wsunbundle_usuarios';
    }


}
