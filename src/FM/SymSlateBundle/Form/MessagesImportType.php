<?php

namespace FM\SymSlateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessagesImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pack_id', 'choice', array('choices' => $options['packs'], 'label' => 'Pack'))
			->add('file','file')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FM\SymSlateBundle\Entity\MessagesImport',
            'packs' => array()
        ));
    }

    public function getName()
    {
        return 'fm_symslatebundle_messagesimporttype';
    }
}
