<?php

namespace CPaint\DrawingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DrawingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('width', 'choice', array(
                'label' => "Size",
                'choices' => array(
                    8 => '8x8',
                    16 => '16x16',
                    32 => '32x32',
                    64 => '64x64',
                    128 => '128x128',
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ));
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CPaint\DrawingBundle\Entity\Drawing'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'drawing';
    }
}
