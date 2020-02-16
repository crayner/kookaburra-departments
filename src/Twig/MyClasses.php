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
 * Date: 10/11/2019
 * Time: 09:34
 */

namespace Kookaburra\Departments\Twig;

use App\Twig\SidebarContentInterface;
use App\Twig\SidebarContentTrait;

/**
 * Class MyClasses
 * @package Kookaburra\Departments\Twig
 */
class MyClasses implements SidebarContentInterface
{
    use SidebarContentTrait;

    /**
     * @var array
     */
    private $classes;

    /**
     * @var string
     */
    private $name = 'My Classes';

    /**
     * @var string
     */
    private $position = 'top';

    /**
     * render
     * @param array $options
     * @return string
     */
    public function render(array $options): string
    {
        try {
            return $this->setContent($this->getTwig()->render('@KookaburraDepartments/sidebar/my_classes.html.twig', ['classes' => $this]))->getContent();
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getClasses(): array
    {
        return $this->classes ?: [];
    }

    /**
     * Classes.
     *
     * @param array $classes
     * @return MyClasses
     */
    public function setClasses(array $classes): MyClasses
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * toArray
     * @return array
     */
    public function toArray(): array
    {
        $this->render([]);
        return ['content' => $this->getContent()];
    }
}