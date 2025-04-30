<?php

namespace App\Form;

use App\Entity\MedikamentListe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\CommaSeparatedStringToArrayTransformer;

class AddMedicinType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('MedikamentNavn', TextType::class, [
        'attr' => [
          'placeholder' => 'Ibuprofen',
          'class' => 'medicinListe-container__AddMedicin-form__input']
      ])
      ->add('TidspunkterTages', TextType::class, [
        'label' => 'Tidspunkter (komma-separeret)',
        'attr' => [
          'placeholder' => 'f.eks. 08:00, 12:00, 18:00',
          'class' => 'medicinListe-container__AddMedicin-form__input'
        ]
      ])
      ->add('Dosis', NumberType::class, [
        'attr' => ['class' => 'medicinListe-container__AddMedicin-form__input']
      ])
      ->add('Enhed', ChoiceType::class, [
        'choices' => [
          'Styk' => 'Styk',
          'mL' => 'mL',
          'g' => 'g',
          'mg' => 'mg',
          'µg' => 'µg',
          'Ingen' => ''
        ],
        'attr' => ['class' => 'medicinListe-container__AddMedicin-form__input']
      ])
      ->add('TimeInterval', IntegerType::class, [
        'attr' => [
          'placeholder' => 'Interval i minutter',
          'class' => 'medicinListe-container__AddMedicin-form__input']
      ])
      ->add('Prioritet', ChoiceType::class, [
        'choices' => [
          'Høj' => 'Høj',
          'Mellem' => 'Mellem',
          'Lav' => 'Lav'
        ],
        'attr' => ['class' => 'medicinListe-container__AddMedicin-form__input']
      ]);

    $builder->get('TidspunkterTages')
      ->addModelTransformer(new CommaSeparatedStringToArrayTransformer());
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      // Configure your form options here
      'data_class' => MedikamentListe::class,
    ]);
  }
}
