<?php

namespace DondrekielAdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class StationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', null, array('label' => 'Identifier'))
            ->add('name', null, array('label' => 'Name'))
            ->add('organizer', null, array('label' => 'Veranstalter'))
            ->add('description', null, array(
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'bbcode'),
                    'label' => 'Beschreibung')
            )
            ->add('status', ChoiceType::class, array('label' => 'Status', 'choices' => array(
                    'inaktiv' => '0',
                    'aktiv' => '1'))
            )
            ->add('locationLat', NumberType::class, array('label' => 'Latitude', 'scale' => 8))
            ->add('locationLng', NumberType::class, array('label' => 'Longitude', 'scale' => 8));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DondrekielAppBundle\Entity\Station'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'DondrekielAdminBundle_game';
    }


}
