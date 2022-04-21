<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Validator as MyAssert;

class UserCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('phoneNumber', TextType::class)
            ->add('cnp', TextType::class)
//            ->add('plainPassword', PasswordType::class)
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'constraints' => [new MyAssert\Password()],
            ])
            ->add(
                'submit',
                SubmitType::class,
                ['attr' =>
                    ['class' =>
                        'rounded-full 
                        px-4 
                        py-3 
                        bg-gradient-to-r 
                        from-indigo-900
                         to-purple-900 
                         text-white
                          text-xl']]
            )
        ;
    }
}
