<?php

namespace App\Form;

use App\Repository\CategoryRepository;
use App\UseCase\Post\Create\Dto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    private CategoryRepository $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class)
            ->add('img', FileType::class, [
                'mapped' => false,
            ])
            ->add('text', TextareaType::class)
            ->add('categories', ChoiceType::class, [
                'multiple' => true,
                'choices' => $this->getChoices(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dto::class,
        ]);
    }

    private function getChoices(): array
    {
        $categoriesList = $this->categories->findAllArray();

        $choices = [];
        foreach ($categoriesList as $item) {
            $choices[$item['title']] = $item['id'];
        }

        return $choices;
    }
}
