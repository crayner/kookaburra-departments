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
 * Time: 14:58
 */

namespace Kookaburra\Departments\Controller;

use App\Container\Container;
use App\Container\ContainerManager;
use App\Container\Panel;
use App\Entity\CourseClass;
use App\Entity\Setting;
use App\Provider\ProviderFactory;
use App\Twig\SidebarContent;
use App\Util\ErrorMessageHelper;
use App\Util\TranslationsHelper;
use Kookaburra\Departments\Entity\Department;
use Kookaburra\Departments\Entity\DepartmentStaff;
use Kookaburra\Departments\Form\DepartmentSettingType;
use Kookaburra\Departments\Form\DepartmentStaffType;
use Kookaburra\Departments\Form\DepartmentType;
use Kookaburra\Departments\Pagination\DepartmentPagination;
use Kookaburra\Departments\Pagination\DepartmentStaffPagination;
use Kookaburra\Departments\Twig\SubjectList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ManageController
 * @package Controller
 */
class ManageController extends AbstractController
{
    /**
     * manage
     * @Route("/manage/", name="manage")
     * @IsGranted("ROLE_ROUTE")
     * @param Request $request
     * @param ContainerManager $manager
     * @param TranslatorInterface $translator
     * @param DepartmentPagination $pagination
     * @param SidebarContent $sidebar
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function manage(Request $request, ContainerManager $manager, TranslatorInterface $translator, DepartmentPagination $pagination, SidebarContent $sidebar)
    {
        $form = $this->createForm(DepartmentSettingType::class, null, ['action' => $this->generateUrl('departments__manage')]);
        ProviderFactory::create(CourseClass::class)->getMyClasses($this->getUser(), $sidebar);

        if ($request->getContentType() === 'json') {
            $data = [];
            $data['status'] = 'success';
            try {
                $data['errors'] = ProviderFactory::create(Setting::class)->handleSettingsForm($form, $request, $translator);
            } catch (\Exception $e) {
                $data['errors'][] = ['class' => 'error', 'message' => $translator->trans('return.error.2')];
            }

            if ($data['status'] === 'success')
                $form = $this->createForm(DepartmentSettingType::class, null, ['action' => $this->generateUrl('departments__manage')]);

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $manager->singlePanel($form->createView());
        $content = ProviderFactory::getRepository(Department::class)->findBy([], ['name' => 'ASC']);
        $pagination->setContent($content)->setPageMax(25)
            ->setPaginationScript();

        return $this->render('@KookaburraDepartments/department/manage.html.twig');
    }

    /**
     * edit
     * @param ContainerManager $manager
     * @param Request $request
     * @param Department|null $department
     * @param string|null $tabName
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/{department}/edit/{tabName}", name="edit")
     * @Route("/add/{tabName}", name="add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function edit(ContainerManager $manager, Request $request, ?Department $department = null, ?string $tabName = 'General')
    {
        if (!$department instanceof Department) {
            $department = new Department();
            $action = $this->generateUrl('departments__add', ['tabName' => $tabName]);
        } else {
            $action = $this->generateUrl('departments__edit', ['department' => $department->getId(), 'tabName' => $tabName]);
        }


        $form = $this->createForm(DepartmentType::class, $department, ['action' => $action]);

        $staffForm = $this->createForm(DepartmentStaffType::class, $department, ['action' => $action]);

        $container = new Container();
        $container->setTarget('formContent')->setSelectedPanel($tabName);
        TranslationsHelper::setDomain('Departments');

        if ($request->getContentType() === 'json') {
            $content = json_decode($request->getContent(), true);
            $data = [];
            $data['status'] = 'success';
            if ($content['formName'] === 'General Form') {
                $form->submit($content);
                if ($form->isValid()) {
                    $id = $department->getId();
                    $provider = ProviderFactory::create(Department::class);
                    $data = $provider->persistFlush($department, $data);
                    if ($data['status'] === 'success') {
                        $form = $this->createForm(DepartmentType::class, $department,
                            ['action' => $this->generateUrl('departments__edit', ['department' => $department->getId(), 'tabName' => 'General'])]
                        );
                        if ($id !== $department->getId()) {
                            ErrorMessageHelper::convertToFlash($data);
                            $data['status'] = 'redirect';
                            $data['redirect'] = $this->generateUrl('departments__edit', ['department' => $department->getId(), 'tabName' => 'General']);
                        }
                    }
                } else {
                    $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                    $data['status'] = 'error';
                }

                $manager->singlePanel($form->createView());
            } elseif ($content['formName'] === 'Staff Form') {
                $manager->singlePanel($staffForm->createView());
                $data = ProviderFactory::create(DepartmentStaff::class)->writeDepartmentStaff($department, $content['newStaff'], $content['role'], $data);
                if ($data['status'] === 'success')
                {
                    $data['status'] = 'redirect';
                    $data['redirect'] = $this->generateUrl('departments__edit', ['department' => $department->getId(), 'tabName' => 'Staff']);
                }
                $manager->singlePanel($staffForm->createView());

            }
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }

        $panel = new Panel('General', 'Departments');
        $container->addForm('General', $form->createView())->addPanel($panel);

        if ($department->getId() > 0) {
            $panel = new Panel('Staff', 'Departments');
            $panel->setPreContent(['currentStaffHeader', 'staffPaginationContent']);
            $container->addForm('Staff', $staffForm->createView())->addPanel($panel);
            $container->addPanel($panel)->setContentLoader([
                [
                    'route' => $this->generateUrl('departments__content_loader', ['department' => $department->getId()]),
                    'target' => 'staffPaginationContent',
                    'type' => 'pagination',
                ],
                [
                    'route' => $this->generateUrl('departments__current_staff_header'),
                    'target' => 'currentStaffHeader',
                    'type' => 'text',
                ],
            ]);
        }

        $manager->addContainer($container)->buildContainers();

        return $this->render('@KookaburraDepartments/department/edit.html.twig',
            [
                'department' => $department,
            ]
        );
    }

    /**
     * delete
     * @param Department $department
     * @param FlashBagInterface $flashBag
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{department}/delete/", name="delete")
     * @IsGranted("ROLE_ROUTE")
     */
    public function delete(Department $department, FlashBagInterface $flashBag, TranslatorInterface $translator)
    {
        $provider = ProviderFactory::create(Department::class);

        $provider->delete($department);

        $provider->getMessageManager()->pushToFlash($flashBag, $translator);

        return $this->redirectToRoute('departments__manage');
    }

    /**
     * delete
     * @param Department $department
     * @param FlashBagInterface $flashBag
     * @param TranslatorInterface $translator
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/staff/{staff}/delete/", name="delete_staff")
     * @Security("is_granted('ROLE_ROUTE', ['departments__manage'])")
     */
    public function deleteStaff(DepartmentStaff $staff, FlashBagInterface $flashBag, TranslatorInterface $translator)
    {
        $provider = ProviderFactory::create(DepartmentStaff::class);

        $provider->delete($staff);

        $provider->getMessageManager()->pushToFlash($flashBag, $translator);

        return $this->redirectToRoute('departments__edit', ['department' => $staff->getDepartment()->getId(), 'tabName' => 'Staff']);
    }

    /**
     * manageContent
     * @Route("/{department}/content/loader/", name="content_loader")
     * @Security("is_granted('ROLE_ROUTE', ['departments__manage'])")
     * @return JsonResponse
     */
    public function manageContent(DepartmentStaffPagination $pagination, ?Department $department)
    {
        try {
            $content = ProviderFactory::getRepository(DepartmentStaff::class)->findStaffByDepartment($department);
            $pagination->setContent($content);
            return new JsonResponse(['content' => $pagination->toArray(), 'pageMax' => $pagination->getPageMax(), 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * currentStaffHeader
     * @Route("/current/staff/header/", name="current_staff_header")
     * @Security("is_granted('ROLE_ROUTE', ['departments__manage'])")
     * @return JsonResponse
     */
    public function currentStaffHeader()
    {
        try {
            return new JsonResponse(['content' => $this->renderView('@KookaburraDepartments/department/current_staff_header.html.twig'), 'status' => 'success'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }
}