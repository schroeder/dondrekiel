<?php

namespace DondrekielAdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TeamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'Name'))
            ->add('username', null, array('label' => 'Identifier'))
            ->add('comment', null, array(
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'bbcode'),
                    'label' => 'Beschreibung')
            )
            ->add('status', ChoiceType::class, array('label' => 'Status', 'choices' => array(
                    'inaktiv' => '0',
                    'aktiv' => '1'))
            )
            ->add('members', null, array('label' => 'Members'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DondrekielAppBundle\Entity\Team'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'DondrekielAdminBundle_team';
    }


}
