<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label'    => 'E-mail adresse',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Indtast din e-mail adresse',
                    'class'       => 'profile-container__form-input'],
            ])
            ->add('FuldeNavn', TextType::class, [
                'label'    => 'Fulde navn',
                'required' => true,
                'attr'     => [
                    'placeholder' => 'Indtast dit fulde navn',
                    'class'       => 'profile-container__form-input'],
            ])
            ->add('telefonNummer', TextType::class, [
                'label'    => 'Telefonnummer',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Indtast dit telefonnummer',
                    'class'       => 'profile-container__form-input'],
            ])
            ->add('addresse', TextType::class, [
                'label'    => 'Adresse',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Indtast din adresse',
                    'class'       => 'profile-container__form-input'],
            ])
            ->add('byNavn', TextType::class, [
                'label'    => 'By',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Indtast din by',
                    'class'       => 'profile-container__form-input'],
            ])
            ->add('postnummer', IntegerType::class, [
                'label'    => 'Postnummer',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Indtast dit postnummer',
                    'class'       => 'profile-container__form-input'],
            ])
            ->add('land', TextType::class, [
                'label'    => 'Land',
                'required' => false,
                'attr'     => [
                    'placeholder' => 'Indtast dit land',
                    'class'       => 'profile-container__form-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
