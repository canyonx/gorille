<?php

namespace App\Form\Newsletter;

use App\Entity\Newsletter\Subscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ],
                'label' => 'Adresse e-mail',
                'attr' => [
                    'placeholder' => 'monemail@exemple.fr'
                ]
            ])
            ->add('isRgpd', CheckboxType::class, [
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez accepter la collecte de votre email'
                    ])
                ],
                'label' => "J'accepte de reçevoir les e-mails du Gorille"
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Je m'inscris à la Newsletter",
                'attr' => [
                    'class' => 'btn btn-warning'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscriber::class,
        ]);
    }
}
