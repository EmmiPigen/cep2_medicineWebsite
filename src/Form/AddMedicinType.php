<?php

namespace App\Form;

use App\Entity\MedikamentListe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddMedicinType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('MedikamentNavn', TextType::class)
      ->add(
        'TidspunkterTages',
        TextType::class,
        [
          'label' => 'Tidspunkter (komma-separeret)',
          'attr' => [
            'placeholder' => 'f.eks. 08:00, 12:00, 18:00',
          ],
        ]
      )
      ->add('Dosis', IntegerType::class)
      ->add('Enhed', TextType::class)
      ->add('TimeInterval', IntegerType::class)
      ->add('Prioritet', TextType::class)
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      // Configure your form options here
      'data_class' => MedikamentListe::class,
    ]);
  }
}
