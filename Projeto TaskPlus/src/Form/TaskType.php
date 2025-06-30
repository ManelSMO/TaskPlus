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
                'attr' => ['class' => 'form-input'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descrição',
                'required' => false,
                'attr' => ['class' => 'form-input'],
            ])
            ->add('dueDate', DateType::class, [
                'label' => 'Data de Vencimento',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['class' => 'form-input'],
            ])
            ->add('isCompleted', CheckboxType::class, [
                'label' => 'Concluída?',
                'required' => false,
                'attr' => ['class' => 'form-checkbox'],
                'label_attr' => ['class' => 'form-checkbox-label'],
            ])
            ->add('taskGroups', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true, 
                'required' => false,
                'label' => 'Grupos da Tarefa',
                'attr' => ['class' => 'form-checkbox-group-wrapper'],
                'choice_attr' => function($choice, $key, $value) {
                    return ['class' => 'form-checkbox'];
                },
                'label_attr' => ['class' => 'form-label'],
                'by_reference' => false, 
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
                'attr' => ['class' => 'form-input'],
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