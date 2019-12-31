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
 * Date: 26/08/2019
 * Time: 11:23
 */

namespace Kookaburra\Departments\Form;

use App\Form\EventSubscriber\FileURLListener;
use App\Form\EventSubscriber\ReactFileListener;
use App\Form\Transform\FileURLToStringTransformer;
use App\Validator\URLOrFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FileURLType
 * @package App\Form\Modules\Departments
 */
class FileURLType extends AbstractType
{
    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * FileURLType constructor.
     * @param RequestStack $stack
     */
    public function __construct(RequestStack $stack)
    {
        $this->stack = $stack;
    }


    /**
     * getParent
     * @return string|null
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * buildForm
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FileURLToStringTransformer($options['file_prefix']))
            ->addEventSubscriber(new FileURLListener($this->stack, 2));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(
            [
                'file_prefix',
            ]
        );
        $resolver->setDefaults([
            'constraints' => [
                new URLOrFile(),
            ],
            'allow_file_upload' => true,
        ]);
    }
}