<?php

namespace FM\SymSlateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PackExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $langs = array();
        foreach($options['languages'] as $lang)
        {
            $langs[$lang->getId()] = $lang;
        }
        $builder
            ->add('pack_id', 'choice', array('choices' => $options['packs']))
            ->add('language_id', 'choice', array('choices' => $langs))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FM\SymSlateBundle\Entity\PackExport',
            'packs' => array(),
            'languages' => array()
        ));
    }

    public function getName()
    {
        return 'fm_symslatebundle_packexporttype';
    }
}
