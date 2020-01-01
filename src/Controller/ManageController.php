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

use App\Container\ContainerManager;
use App\Entity\Setting;
use App\Provider\ProviderFactory;
use App\Util\TranslationsHelper;
use Kookaburra\Departments\Entity\Department;
use Kookaburra\Departments\Form\DepartmentSettingType;
use Kookaburra\Departments\Pagination\DepartmentPagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
     */
    public function manage(Request $request, ContainerManager $manager, TranslatorInterface $translator, DepartmentPagination $pagination)
    {
        $form = $this->createForm(DepartmentSettingType::class, null, ['action' => $this->generateUrl('departments__manage')]);

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
     * @Route("/{department}/edit/", name="edit")
     * @Route("/add/", name="add")
     * @IsGranted("ROLE_ROUTE")
     */
    public function edit(ContainerManager $manager, Request $request, ?Department $department = null)
    {
        if (!$department instanceof Department) {
            $department = new Department();
            $action = $this->generateUrl('departments__add');
        } else {
            $action = $this->generateUrl('departments__edit', ['department' => $department->getId()]);
        }

        $form = $this->createForm(DepartmentType::class, $department, ['action' => $action]);

        if ($request->getContentType() === 'json') {
            $content = json_decode($request->getContent(), true);
            dump($content);
            $form->submit($content);
            $data = [];
            $data['status'] = 'success';
            if ($form->isValid()) {
                $id = $department->getId();
                $provider = ProviderFactory::create(Department::class);
                $data = $provider->persistFlush($department, $data);
                if ($data['status'] === 'success')
                    $form = $this->createForm(DepartmentType::class, $department, ['action' => $this->generateUrl('departments__edit', ['department' => $department->getId()])]);
            } else {
                $data['errors'][] = ['class' => 'error', 'message' => TranslationsHelper::translate('return.error.1', [], 'messages')];
                $data['status'] = 'error';
            }

            $manager->singlePanel($form->createView());
            $data['form'] = $manager->getFormFromContainer('formContent', 'single');

            return new JsonResponse($data, 200);
        }
        $manager->singlePanel($form->createView());

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
}