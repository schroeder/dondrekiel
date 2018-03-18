<?php

namespace DondrekielAdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Oh\GoogleMapFormTypeBundle\Form\Type\GoogleMapType;


class GameType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', null, array('label' => 'Identifier'))
            ->add('name', null, array('label' => 'Spielname'))
            ->add('description', null, array(
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'bbcode'),
                    'label' => 'Beschreibung')
            )
            ->add('status', ChoiceType::class, array('label' => 'Status', 'choices' => array(
                    'inaktiv' => '0',
                    'aktiv' => '1'))
            )
            ->add('locationLat', null, array('label' => 'Latitude'))
            ->add('locationLng', null, array('label' => 'Longitude'));
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
