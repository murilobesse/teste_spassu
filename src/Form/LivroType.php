<?php

namespace App\Form;

use App\Entity\Livro;
use App\Entity\Autor;
use App\Entity\Assunto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\DataTransformer\MoneyToFloatTransformer;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class LivroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: O Estrangeiro',
                    'required' => true
                ],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(message: 'O título é obrigatório.'),
                    new Length(max: 40, maxMessage: 'O título não pode ter mais de 40 caracteres.')
                ]
            ])
            ->add('editora', TextType::class, [
                'label' => 'Editora',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Editora Record',
                    'required' => true
                ],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(message: 'A editora é obrigatória.'),
                    new Length(max: 40, maxMessage: 'A editora não pode ter mais de 40 caracteres.')
                ]
            ])
            ->add('edicao', NumberType::class, [
                'label' => 'Edição',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 1ª Edição',
                    'required' => true
                ],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(message: 'A edição é obrigatória.'),
                    new Length(max: 40, maxMessage: 'A edição não pode ter mais de 40 caracteres.')
                ]
            ])
            ->add('anoPublicacao', NumberType::class, [
                'label' => 'Ano de Publicação',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 2020',
                    'maxlength'=> 4,
                    'required' => true
                ],
                'empty_data' => '',
                'constraints' => [
                    new NotBlank(message: 'O ano de publicação é obrigatório.'),
                    new Length(max: 4, maxMessage: 'O ano de publicação não pode ter mais de 4 caracteres.')
                ]
            ])
            ->add('valor', TextType::class, [
                'label' => 'Valor (R$)',
                //'currency' => 'BRL',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: R$ 19,90',
                    'type' => 'text',
                    'required' =>true
                ],
                'constraints' => [
                    new NotBlank(message: 'O valor é obrigatório.'),
                    new PositiveOrZero(message: 'O valor não pode ser negativo.')
                ]
            ])
            ->add('autores', EntityType::class, [
                'class' => Autor::class,
                'choice_label' => 'nome',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Autores',
                'attr' => [
                    'class' => 'form-select',
                    'placeholder' => 'Selecione os autores...',
                    'autocomplete' => 'off',
                ],
                'constraints' => [new NotBlank(message: 'Selecione pelo menos um autor.')],
            ])
            ->add('assuntos', EntityType::class, [
                'class' => Assunto::class,
                'choice_label' => 'descricao',
                'multiple' => true,
                'expanded' => false,
                'label' => 'Assuntos',
                'attr' => [
                    'class' => 'form-select',
                    'placeholder' => 'Selecione os assuntos...',
                    'autocomplete' => 'off',
                ],
                'constraints' => [new NotBlank(message: 'Selecione pelo menos um assunto.')]
            ]);

        $builder->get('valor')->addModelTransformer(new MoneyToFloatTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livro::class,
        ]);
    }
}
