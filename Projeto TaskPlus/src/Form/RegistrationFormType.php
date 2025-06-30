<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Adicionar para firstName e lastName
use Symfony\Component\Form\Extension\Core\Type\RepeatedType; // Adicionar para repetir senha
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
            ->add('firstName', TextType::class, [ // Novo campo
                'label' => 'Nome',
                'attr' => ['autocomplete' => 'given-name', 'class' => 'form-input'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, insira seu nome.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Seu nome deve ter pelo menos {{ limit }} caracteres.',
                        'max' => 255,
                        'maxMessage' => 'Seu nome deve ter no máximo {{ limit }} caracteres.',
                    ]),
                ],
            ])
            ->add('lastName', TextType::class, [ // Novo campo
                'label' => 'Sobrenome',
                'attr' => ['autocomplete' => 'family-name', 'class' => 'form-input'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, insira seu sobrenome.',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Seu sobrenome deve ter pelo menos {{ limit }} caracteres.',
                        'max' => 255,
                        'maxMessage' => 'Seu sobrenome deve ter no máximo {{ limit }} caracteres.',
                    ]),
                ],
            ])
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
            ->add('plainPassword', RepeatedType::class, [ // Alterado para RepeatedType
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'label' => 'Senha',
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-input'],
                ],
                'second_options' => [
                    'label' => 'Repetir Senha',
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-input'],
                ],
                'invalid_message' => 'As senhas devem coincidir.', // Mensagem de erro para senhas que não coincidem
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
                'required' => true, // Geralmente termos de uso são obrigatórios
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
            'csrf_protection' => false,
        ]);
    }
}