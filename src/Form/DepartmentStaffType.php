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
 * Date: 3/01/2020
 * Time: 09:16
 */

namespace Kookaburra\Departments\Form;

use App\Form\Type\DisplayType;
use App\Form\Type\EnumType;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactFormType;
use App\Form\Type\SimpleArrayType;
use App\Util\TranslationsHelper;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Kookaburra\Departments\Entity\Department;
use Kookaburra\Departments\Entity\DepartmentStaff;
use Kookaburra\UserAdmin\Entity\Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class DepartmentStaffType
 * @package Kookaburra\Departments\Form
 */
class DepartmentStaffType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('staffTitle', HeaderType::class,
                [
                    'label' => 'New Staff',
                    'panel' => 'Staff',
                ]
            )
            ->add('newStaff', EntityType::class,
                [
                    'label' => 'Staff',
                    'help' => 'Use Control, Command and/or Shift to select multiple.',
                    'class' => Person::class,
                    'choice_label' => 'fullNameReversed',
                    'multiple' => true,
                    'mapped' => false,
                    'attr' => [
                        'size' => '8',
                    ],
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->leftJoin('p.staff', 's')
                            ->select(['p','s'])
                            ->where('s.id IS NOT NULL')
                            ->orderBy('p.surname')
                            ->addOrderBy('p.firstName')
                        ;
                    },
                ]
            )
            ->add('role', EnumType::class,
                [
                    'label' => 'Role',
                    'mapped' => false,
                    'placeholder' => TranslationsHelper::translate('Please select...', [], 'messages'),
                    'choice_list_class' => DepartmentStaff::class,
                ]
            )
            ->add('formName', HiddenType::class,
                [
                    'data' => 'Staff Form',
                    'mapped' => false,
                ]
            )
            ->add('submit', SubmitType::class,
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