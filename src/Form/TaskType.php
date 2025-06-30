<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Group;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Título da Tarefa',
                'attr' => ['class' => 'form-input'], // Adicionado para estilo
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descrição',
                'required' => false,
                'attr' => ['class' => 'form-input'], // Adicionado para estilo
            ])
            ->add('dueDate', DateType::class, [
                'label' => 'Data de Vencimento',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-input'], // Adicionado para estilo
            ])
            ->add('isCompleted', CheckboxType::class, [
                'label' => 'Concluída?',
                'required' => false,
                'attr' => ['class' => 'form-checkbox'], // Adicionado para estilo
                'label_attr' => ['class' => 'form-checkbox-label'], // Adicionado para estilo
            ])
            // Campo para atribuir a tarefa a um usuário (opcional, se não for feito automaticamente no controller)
            // Removido por padrão, pois o controller já atribui ao usuário logado
            /*
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => false,
                'expanded' => false,
                'required' => true,
                'placeholder' => 'Selecione o Usuário',
                'label' => 'Atribuir a Usuário',
                'attr' => ['class' => 'form-input'],
            ])
            */
            // Campo para selecionar grupos
            ->add('taskGroups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, // Exibe como checkboxes
                'required' => false,
                'label' => 'Grupos da Tarefa',
                'attr' => ['class' => 'form-checkbox-group-wrapper'], // Wrapper para estilo
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-checkbox']; // Estilo para checkboxes individuais
                },
                'label_attr' => ['class' => 'form-label'], // Estilo para o label principal do campo
            ])
            ->add('priority', ChoiceType::class, [
                'choices' => [
                    'Baixa' => 'low',
                    'Média' => 'medium',
                    'Alta' => 'high',
                ],
                'label' => 'Prioridade',
                'required' => false,
                'placeholder' => 'Selecione a Prioridade',
                'attr' => ['class' => 'form-input'], // Adicionado para estilo
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}