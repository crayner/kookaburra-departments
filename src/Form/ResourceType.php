<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * User: craig
 * Date: 13/08/2019
 * Time: 10:37
 */

namespace Kookaburra\Departments\Form;

use App\Entity\Department;
use App\Entity\DepartmentResource;
use App\Form\Type\HiddenEntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ResourceType
 * @package App\Form\Modules\Departments
 */
class ResourceType extends AbstractType
{
    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'Resource Name',
                    'row_style' => 'collection_column',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('type', ChoiceType::class,
                [
                    'label' => 'Resource Type',
                    'choices' => [
                        'Link' => 'Link',
                        'File' => 'File',
                    ],
                    'empty_data' => 'Link',
                    'on_change' => 'manageLinkOrFile',
                    'row_style' => 'collection_column',
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add('url', FileURLType::class,
                [
                    'label' => 'Resource Location',
                    'file_prefix' => 'resource',
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'row_style' => 'collection_column',
                ]
            )
            ->add('department', HiddenEntityType::class,
                [
                    'class' => Department::class,
                    'row_style' => 'hidden',
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
                'data_class' => DepartmentResource::class,
                'translation_domain' => 'messages',
            ]
        );
    }
}