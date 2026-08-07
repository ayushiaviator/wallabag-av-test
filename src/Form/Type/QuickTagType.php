<?php

namespace Wallabag\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wallabag\Entity\Tag;

class QuickTagType extends AbstractType
{
    public const MAX_LENGTH = 40;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'tag.new.placeholder',
                    'max_length' => self::MAX_LENGTH,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            // the control is submitted with fetch() from the entry list, where we
            // don't render a full form, so the token would never be sent
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'quick_tag';
    }
}
