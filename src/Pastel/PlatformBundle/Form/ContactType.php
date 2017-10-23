<?php

namespace Pastel\PlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Vihuvac\Bundle\RecaptchaBundle\Form\Type\VihuvacRecaptchaType;
use Vihuvac\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaTrue;



class ContactType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName',  TextType::class)
            ->add('email', EmailType::class)
            ->add('message', TextareaType::class)
            ->add('recaptcha', VihuvacRecaptchaType::class, array(
                'constraints' => array(
                    new RecaptchaTrue()
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'pastel_platformbundle_contact_form';
    }
}