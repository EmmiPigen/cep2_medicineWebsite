<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class CaregiverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('omsorgspersonNavn', TextType::class, [
                'label' => 'Kontaktpersonens Navn',
                'required' => true,
            ])
            ->add('omsorgspersonTelefon', TextType::class, [
                'label' => 'Kontaktpersonens Telefon',
                'required' => true,
            ])
            ->add('omsorgspersonEmail', TextType::class, [
                'label' => 'Kontaktpersonens Email',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
