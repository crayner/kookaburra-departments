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
 * Time: 09:21
 */

namespace Kookaburra\Departments\Form;

use Kookaburra\Departments\Entity\Department;
use App\Form\EventSubscriber\FileOrLinkURLSubscriber;
use App\Form\Type\HeaderType;
use App\Form\Type\ReactCollectionType;
use App\Form\Type\ReactFormType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class EditType
 * @package App\Form\Modules\Departments
 */
class EditType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * @var string
     */
    private $targetDir;

    /**
     * FileOrLinkURLSubscriber constructor.
     * @param RequestStack $stack
     * @param string $targetDir
     */
    public function __construct(RequestStack $stack, string $targetDir)
    {
        $this->stack = $stack;
        $this->targetDir = $targetDir;
    }

    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('overview', HeaderType::class,
                [
                    'label' => 'Overview',
                ]
            )
            ->add('blurb', CKEditorType::class,
                [
                    'label' => 'Description',
                    'required' => false,
                    'row_style' => 'single',
                ]
            )
            ->add('resource_header', HeaderType::class,
                [
                    'label' => 'Resources',
                ]
            )
            ->add('resources', ReactCollectionType::class,
                [
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_type' => ResourceType::class,
                    'prototype' => true,
                    'header_row' => true,
                    'row_style' => 'single',
                    'element_delete_route' => $options['resource_delete_route'],
                    'element_delete_options' => ['__id__' => 'id', '__department__' => 'department'],
                ]
            )
            ->add('id', HiddenType::class)
            ->add('submit', SubmitType::class,
                [
                    'row_style' => 'single',

                ]
            )
        ;
        $builder->get('resources')->addEventSubscriber(new FileOrLinkURLSubscriber($this->stack, $this->targetDir));
    }

    /**
     * configureOptions
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Department::class,
                'translation_domain' => 'messages',
                'columns' => 1,
                'target' => 'formContent',
                'attr' => [
                    'autoComplete' => 'on',
                    'encType' => 'multipart/form-data',
                    'className' => 'smallIntBorder fullWidth standardForm',
                ]
            ]
        );
        $resolver->setRequired([
            'resource_manager',
            'resource_delete_route',
        ]);
    }

    /**
     * getBlockPrefix
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'department_edit';
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