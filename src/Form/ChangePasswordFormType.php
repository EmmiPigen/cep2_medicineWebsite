<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'options'         => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class'        => 'form-control',
                    ],
                ],
                'first_options'   => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Indtast venligst dit kodeord',
                        ]),
                        new Length([
                            'min'        => 6,
                            'minMessage' => 'Dit kodeord skal være mindst {{ limit }} tegn langt og indeholde midst 1 stort bogstav og 1 tal.',
                            // max length allowed by Symfony for security reasons
                            'max'        => 4096,
                        ]),
                    ],
                    'label'       => 'Nye adgangskode',
                ],
                'second_options'  => [
                    'label' => 'Gentag adgangskode',
                ],
                'invalid_message' => 'De to adgangskoder skal være ens.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped'          => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
