<?php

namespace Nico\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints as Assert;

class CircuitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'label' => 'Nom du club/Terrain',
                'constraints' => array(
                    new Assert\Type('string'),
                    new Assert\Length(array(
                        'max' => 50,
                        'maxMessage' => '50 caractères maximum',
                    )),
                ),
            ))
            ->add('hours', TextareaType::class, array(
                'label' => 'Horaires/Jours d\'ouverture',
                'constraints' => array(
                    new Assert\Type('string'),
                    new Assert\Length(array(
                        'max' => 100,
                        'maxMessage' => '100 caractères maximum',
                    )),
                ),
            ))
            ->add('licence', TextareaType::class, array(
                'label' => 'Licence(s) acceptées',
                'constraints' => array(
                    new Assert\Type('string'),
                    new Assert\Length(array(
                        'max' => 50,
                        'maxMessage' => '50 caractères maximum',
                    )),
                ),
            ))
            ->add('description', TextareaType::class, array(
                'label' => 'Description',
                'constraints' => array(
                    new Assert\Type('string'),
                    new Assert\Length(array(
                        'max' => 500,
                        'maxMessage' => '500 caractères maximum',
                    )),
                ),
            ))
            ->add('latitude', TextType::class, array(
                'label_attr' => array(
                    'hidden' => '',
                ),
                'attr' => array(
                    'id' => 'latitude',
                    'hidden' => '',
                ),
                'constraints' => new Assert\Regex(array(
                    'pattern' => '/[0-9*]/',
                    'message' => 'Ne correspond pas a des coordonnées',
                )),
            ))
            ->add('longitude', TextType::class, array(
                'label_attr' => array(
                    'hidden' => '',
                ),
                'attr' => array(
                    'id' => 'longitude',
                    'hidden' => '',
                ),
                'constraints' => new Assert\Regex(array(
                    'pattern' => '/[0-9*]/',
                    'message' => 'Ne correspond pas a des coordonnées',
                )),
            ))
            ->add('address', TextType::class, array(
                'constraints' => array(
                    new Assert\Type('string'),
                )
            ))
            ->add('image', ImageType::class, array(
                'label_attr' => array(
                    'hidden' => '',
                ),
            ))
            ->add('owner', OwnerType::class, array(
                'label_attr' => array(
                    'hidden' => '',
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
            'data_class' => 'Nico\AppBundle\Entity\Circuit'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'nico_appbundle_circuit';
    }


}
