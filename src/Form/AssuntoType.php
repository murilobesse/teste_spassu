<?php

namespace App\Form;

use App\Entity\Assunto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class AssuntoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('descricao', TextType::class, [
                'label' => 'Descrição do Assunto',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Distopia',
                    'required' => true
                ],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(['message' => 'A descrição do assunto é obrigatória.']),
                    new Length(['max' => 40, 'maxMessage' => 'A descrição não pode ter mais de 40 caracteres.'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Assunto::class,
        ]);
    }
}
