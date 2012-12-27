<?php

namespace FM\Bundle\SlowShowBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user_id')
            ->add('class')
            ->add('arguments')
            ->add('status')
            ->add('progress')
            ->add('created')
            ->add('updated')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FM\Bundle\SlowShowBundle\Entity\Task'
        ));
    }

    public function getName()
    {
        return 'fm_bundle_slowshowbundle_tasktype';
    }
}
