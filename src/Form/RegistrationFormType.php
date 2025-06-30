<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => ['autocomplete' => 'email', 'class' => 'form-input'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, insira um e-mail.',
                    ]),
                    new Email([
                        'message' => 'O e-mail "{{ value }}" não é um e-mail válido.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Senha',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', 'class' => 'form-input'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, insira uma senha.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Sua senha deve ter pelo menos {{ limit }} caracteres.',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'required' => false, // Set to true if agreement is mandatory
                'constraints' => [
                    new IsTrue([
                        'message' => 'Você deve concordar com nossos termos.',
                    ]),
                ],
                'label' => 'Concordo com os termos de uso',
                'attr' => ['class' => 'form-checkbox'],
                'label_attr' => ['class' => 'form-checkbox-label'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // --- ADICIONE ESTA LINHA PARA DESABILITAR CSRF APENAS NESTE FORMULÁRIO ---
            'csrf_protection' => false,
            // ----------------------------------------------------------------------
        ]);
    }
}