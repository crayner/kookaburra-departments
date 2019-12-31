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
 * Date: 22/11/2019
 * Time: 08:16
 */

namespace Kookaburra\Departments\Twig;

use App\Entity\Course;
use App\Entity\Department;
use App\Twig\SidebarContentInterface;
use App\Twig\SidebarContentTrait;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ClassList
 * @package Kookaburra\Departments\Twig
 */
class ClassList implements SidebarContentInterface
{
    use SidebarContentTrait;

    /**
     * @var string
     */
    private $position = 'top';

    /**
     * @var Course
     */
    private $course;

    /**
     * @var Department
     */
    private $department;

    /**
     * render
     * @param array $options
     * @return string
     */
    public function render(array $options): string
    {
        try {
            return $this->getTwig()->render('@KookaburraDepartments/sidebar/course_classes.html.twig', ['content' => $this]);
        } catch (LoaderError $e) {
            return '';
        } catch (RuntimeError $e) {
            return '';
        } catch (SyntaxError $e) {
            return '';
        }
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * Course.
     *
     * @param Course $course
     * @return ClassList
     */
    public function setCourse(Course $course): ClassList
    {
        $this->course = $course;
        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->department;
    }

    /**
     * Department.
     *
     * @param Department $department
     * @return ClassList
     */
    public function setDepartment(Department $department): ClassList
    {
        $this->department = $department;
        return $this;
    }
}