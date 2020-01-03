<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2020 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 2/01/2020
 * Time: 07:48
 */

namespace Kookaburra\Departments\Form;

use App\Form\Type\DisplayType;
use App\Form\Type\EnumType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use App\Form\Type\SimpleArrayType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kookaburra\Departments\Entity\Department;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DepartmentType
 * @package Kookaburra\Departments\Form
 */
class DepartmentType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('generalTitle', HeaderType::class,
                [
                    'label' => 'General',
                    'panel' => 'General',
                ]
            )
        ;
        if (intval($options['data']->getId()) === 0)
        {
            $builder
                ->add('type', EnumType::class,
                    [
                        'label' => 'Type',
                        'panel' => 'General',
                    ]
                )
            ;
        } else {
            $builder
                ->add('type', DisplayType::class,
                    [
                        'label' => 'Type',
                        'panel' => 'General',
                    ]
                )
            ;
        }
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'Name',
                    'panel' => 'General',
                ]
            )
            ->add('nameShort', TextType::class,
                [
                    'label' => 'Abbreviation',
                    'panel' => 'General',
                ]
            )
            ->add('subjectListing', SimpleArrayType::class,
                [
                    'label' => 'Subject Listing',
                    'panel' => 'General',
                ]
            )
            ->add('blurbName', HeaderType::class,
                [
                    'label' => 'Blurb',
                    'header_type' => 'h6',
                    'panel' => 'General',
                ]
            )
            ->add('blurb', CKEditorType::class,
                [
                    'row_style' => 'single',
                    'panel' => 'General',
                ]
            )
            ->add('submit', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'General',
                ]
            )
            ->add('staffTitle', HeaderType::class,
                [
                    'label' => 'New Staff',
                    'panel' => 'Staff',
                ]
            )
            ->add('formName', HiddenType::class,
                [
                    'data' => 'General Form',
                    'mapped' => false,
                ]
            )
                ->add('submit2', SubmitType::class,
                [
                    'label' => 'Submit',
                    'panel' => 'Staff',
                ]
            )
        ;
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'Departments',
                'data_class' => Department::class,
            ]
        );
    }

    /**
     * getParent
     * @return string|null
     */
    public function getParent()
    {
        return ReactFormType::class;
    }
}