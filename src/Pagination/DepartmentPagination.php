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
 * Date: 1/01/2020
 * Time: 15:31
 */

namespace Kookaburra\Departments\Pagination;

use App\Manager\Entity\PaginationAction;
use App\Manager\Entity\PaginationColumn;
use App\Manager\Entity\PaginationRow;
use App\Manager\ReactPaginationInterface;
use App\Manager\ReactPaginationManager;
use App\Util\TranslationsHelper;

/**
 * Class DepartmentPagination
 * @package Kookaburra\Departments\Pagination
 */
class DepartmentPagination extends ReactPaginationManager
{
    /**
     * execute
     * @return ReactPaginationInterface
     */
    public function execute(): ReactPaginationInterface
    {
        TranslationsHelper::setDomain('Departments');
        $row = new PaginationRow();

        $column = new PaginationColumn();
        $column->setLabel('Name')
            ->setContentKey('name')
            ->setSort(true)
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Type')
            ->setContentKey('type')
            ->setSort(true)
            ->setClass('column relative pr-4 cursor-pointer widthAuto')
        ;
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Abbreviation')
            ->setContentKey('abbr')
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $column = new PaginationColumn();
        $column->setLabel('Staff')
            ->setContentKey('staff')
            ->setClass('column relative pr-4 cursor-pointer widthAuto');
        $row->addColumn($column);

        $action = new PaginationAction();
        $action->setTitle('Edit')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('fas fa-edit fa-fw fa-1-5x text-gray-700')
            ->setRoute('departments__edit')
            ->setRouteParams(['department' => 'id']);
        $row->addAction($action);

        $action = new PaginationAction();
        $action->setTitle('Delete')
            ->setAClass('')
            ->setColumnClass('column p-2 sm:p-3')
            ->setSpanClass('far fa-trash-alt fa-fw fa-1-5x text-gray-700')
            ->setRoute('departments__delete')
            ->setDisplayWhen('canDelete')
            ->setOnClick('areYouSure')
            ->setRouteParams(['department' => 'id']);
        $row->addAction($action);

        $this->setRow($row);
        return $this;
    }
}