<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Project;
use App\Entity\TimeTracker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeTrackerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Task Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Name',
                    'class' => 'form-control',
                    'style' => 'width: 400px;'
                ]
            ])
            ->add('start_date', DateType::class, [
                'label' => 'Start Date',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'width: 200px;'
                ]
            ])
            ->add('start_time', TimeType::class, [
                'label' => 'Start Time',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'width: 200px;'
                ]
            ])
            ->add('end_time', TimeType::class, [
                'label' => 'End Time',
                'input'  => 'datetime',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'width: 200px;'
                ]
            ])
            ->add('project', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'name',
                'label' => 'Project',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'width: 400px;'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TimeTracker::class,
        ]);
    }
}
