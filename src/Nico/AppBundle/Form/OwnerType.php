<?php

namespace Nico\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

use Symfony\Component\Validator\Constraints as Assert;

class OwnerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nom du responsable',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                    new Assert\Length(array(
                        'max' => 50,
                        'maxMessage' => '50 caractères maximum',
                    )),
                ),
            ))
            ->add('firstName', TextType::class, array(
                'label' => 'Prénom du responsable',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                    new Assert\Length(array(
                        'max' => 50,
                        'maxMessage' => '50 caractères maximum',
                    )),
                ),
            ))
            ->add('email', EmailType::class, array(
                'label' => 'Email',
                'constraints' => new Assert\NotBlank(),
            ))
            ->add('phoneNumber', NumberType::class, array(
                'label' => 'Téléphone',
                'constraints' => array(
                    new Assert\Regex(array(
                        'pattern' => '/[0-9{10}]/',
                        'message' => 'Ne correspond pas a un numéro de téléphone',
                    )),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nico\AppBundle\Entity\Owner'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'nico_appbundle_owner';
    }


}
