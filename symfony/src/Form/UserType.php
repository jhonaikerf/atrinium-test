<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('roles', ChoiceType::class,
                [
                    'choices' => [
                        'Admin' => 'ROLE_ADMIN',
                        'Client' => 'ROLE_CLIENT',
                    ],
                    'multiple' => true,
                    'expanded' => false,
                ]
            )
            ->add('sector');


        $builder->addEventListener(FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use ($builder) {
                $administrator = $event->getData();
                if (!$administrator || null === $administrator->getId()) {
                    $event->getForm()->add('password', PasswordType::class, [
                        'required' => true,
                        'mapped' => false,
                    ]);
                } else {
                    $event->getForm()->add('password', PasswordType::class, [
                        'required' => false,
                        'mapped' => false,
                    ]);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
