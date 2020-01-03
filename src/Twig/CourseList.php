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
 * Time: 07:41
 */

namespace Kookaburra\Departments\Twig;

use Kookaburra\Departments\Entity\Department;
use App\Twig\SidebarContentInterface;
use App\Twig\SidebarContentTrait;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class CourseList
 * @package Kookaburra\Departments\Twig
 */
class CourseList implements SidebarContentInterface
{
    use SidebarContentTrait;

    /**
     * @var array
     */
    private $courses;

    /**
     * @var Department
     */
    private $Department;

    /**
     * @var int
     */
    private $priority = 10;

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
            return $this->getTwig()->render('@KookaburraDepartments/sidebar/course_list.html.twig', ['content' => $this]);
        } catch (LoaderError $e) {
            return '';
        } catch (RuntimeError $e) {
            return '';
        } catch (SyntaxError $e) {
            return '';
        }
    }

    /**
     * @return array
     */
    public function getCourses(): array
    {
        return $this->courses;
    }

    /**
     * Courses.
     *
     * @param mixed $courses
     * @return CourseList
     */
    public function setCourses($courses)
    {
        if (is_string($courses))
            $courses = explode(',', $courses);
        $this->courses = $courses;
        return $this;
    }

    /**
     * @return Department
     */
    public function getDepartment(): Department
    {
        return $this->Department;
    }

    /**
     * Department.
     *
     * @param Department $Department
     * @return CourseList
     */
    public function setDepartment(Department $Department): CourseList
    {
        $this->Department = $Department;
        return $this;
    }
}