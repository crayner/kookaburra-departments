<?php
/**
 * Created by PhpStorm.
 *
 * kookaburra
 * (c) 2019 Craig Rayner <craig@craigrayner.com>
 *
 * User: craig
 * Date: 22/11/2019
 * Time: 07:14
 */

namespace Kookaburra\Departments\Twig;


use App\Twig\SidebarContentInterface;
use App\Twig\SidebarContentTrait;

class SubjectList implements SidebarContentInterface
{
    use SidebarContentTrait;

    /**
     * @var array
     */
    private $subjects;

    /**
     * @var int
     */
    private $priority = 5;

    /**
     * @var string
     */
    private $position = 'top';

    /**
     * render
     * @param array $options
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render(array $options): string
    {
        return $this->getTwig()->render('@KookaburraDepartments/sidebar/subject_list.html.twig', ['subjects' => $this]);
    }

    /**
     * @return array
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * Subjects.
     *
     * @param array $subjects
     * @return SubjectList
     */
    public function setSubjects($subjects): SubjectList
    {
        if (is_string($subjects))
            $subjects = explode(',', $subjects);
        foreach($subjects as $q=>$w)
            $subjects[$q] = trim($w);
        $this->subjects = $subjects;
        return $this;
    }
}