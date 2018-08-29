<?php

namespace DondrekielAdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
            ->add('email', null, array('label' => 'Email', 'data' => 'info@dondrekiel.de'))
            ->add('plainPassword', null, array('label' => 'Password', 'required' => false))
            ->add('comment', null, array(
                    'attr' => array('class' => 'tinymce', 'data-theme' => 'bbcode'),
                    'label' => 'Beschreibung',
                    'data' => 'Noch eine Station')
            )
            ->add('status', ChoiceType::class, array('label' => 'Status', 'choices' => array(
                    'aktiv' => '1', 'inaktiv' => '0'))
            )
            ->add('isTeam', ChoiceType::class, array('label' => 'Ist Team', 'choices' => array(
                    'ja' => '1',
                    'nein' => '0'))
            )->add('enabled', HiddenType::class, array('data' => '1')
            )->add('locationLng', HiddenType::class, array('data' => "7.828652")
            )->add('locationLat', HiddenType::class, array('data' => "51.844039")
            );
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
