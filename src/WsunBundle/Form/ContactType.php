<?php

namespace Acme\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints;

class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email', 'email')
            ->add('message', 'textarea')
            ->add('privacy', 'checkbox', array('label' => 'I authorize.'))
            ->add('button', 'submit', array('label' => 'Send'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $constraints = new Constraints\Collection(array('fields' => array(
            'name'    => array(new Constraints\NotBlank(), new Constraints\Length(array('max' => 100))),
            'email'   => array(new Constraints\NotBlank(), new Constraints\Email()),
            'message' => array(new Constraints\NotBlank(), new Constraints\Length(array('max' => 900))),
            'privacy' => new Constraints\NotNull(),
        )));
        $resolver
            ->setDefaults(array('constraints' => $constraints, 'attr' => array('novalidate' => true)))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'info';
    }
}
