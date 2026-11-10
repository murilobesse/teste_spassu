<?php

namespace App\Form;

use App\Entity\Autor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<Autor>
 */
class AutorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome', TextType::class, [
                'label' => 'Nome do Autor',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Albert Camus',
                    'required' => true,
                ],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(message: 'O nome do autor é obrigatório.'),
                    new Length(max: 40, maxMessage: 'O nome não pode ter mais de 40 caracteres.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Autor::class,
        ]);
    }
}
