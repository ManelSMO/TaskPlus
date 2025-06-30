<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Novo: Para múltiplos emails

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nome do Grupo',
                'attr' => ['placeholder' => 'Ex: Equipe de Desenvolvimento', 'class' => 'form-input']
            ])
            ->add('collaboratorEmails', TextareaType::class, [
                'label' => 'E-mails dos Colaboradores (separados por vírgula ou nova linha)',
                'mapped' => false, // Este campo não está diretamente mapeado para a entidade Group
                'required' => false, // É opcional adicionar colaboradores
                'attr' => [
                    'class' => 'form-input',
                    'rows' => 3, // Para permitir múltiplas linhas
                    'placeholder' => 'ex: email1@example.com, email2@example.com'
                ],
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}