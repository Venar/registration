<?php

namespace AppBundle\Controller\Manage;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ManageController extends Controller
{
    /**
     * @Route("/manage/")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return Response
     */
    public function listRegistrationsPage(Request $request)
    {
        $searchText = $request->query->get('search');
        $page = $request->query->get('page');

        $registrationTypeID = '';
        $registrationTypeDescription = 'All';
        if ($request->query->has('RegistrationType_ID')) {
            $registrationTypeID = $request->query->get('RegistrationType_ID');
            $registrationType = $this->getDoctrine()
                ->getRepository('AppBundle:Registrationtype')
                ->find($registrationTypeID);
            if ($registrationType) {
                $registrationTypeDescription = $registrationType->getDescription();
            } else {
                $registrationTypeID = '';
            }
        }

        $registrationStatusID = '';
        $registrationStatusDescription = 'All';
        if ($request->query->has('RegistrationStatus_ID')) {
            $registrationStatusID = $request->query->get('RegistrationStatus_ID');
            $registrationStatus = $this->getDoctrine()
                ->getRepository('AppBundle:Registrationstatus')
                ->find($registrationStatusID);
            if ($registrationStatus) {
                $registrationStatusDescription = $registrationStatus->getDescription();
            } else {
                $registrationStatusID = '';
            }
        }

        $badgeTypeID = '';
        $badgeTypeDescription = 'All';
        if ($request->query->has('BadgeType_ID')) {
            $badgeTypeID = $request->query->get('BadgeType_ID');
            $badgeType = $this->getDoctrine()
                ->getRepository('AppBundle:Badgetype')
                ->find($badgeTypeID);
            if ($badgeType) {
                $badgeTypeDescription = $badgeType->getDescription();
            } else {
                $badgeTypeID = '';
            }
        }

        if ($page <= 1 || !is_numeric($page)) {
            $page = 1;
        }

        $vars = [
            'current_RegistrationType_ID' => $registrationTypeID,
            'current_RegistrationStatus_ID' => $registrationStatusID,
            'current_BadgeType_ID' => $badgeTypeID,
            'current_RegistrationType' => $registrationTypeDescription,
            'current_RegistrationStatus' => $registrationStatusDescription,
            'current_BadgeType' => $badgeTypeDescription,
            'searchText' => $searchText,
            'page' => $page,
        ];

        return $this->render('manage/manage.html.twig', $vars);
    }

    /**
     * @Route("/registrationlist")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function ajaxRegistrationList(Request $request)
    {
        $returnJson = [];

        $searchText = $request->query->get('search');
        $page = $request->query->get('page');

        $registrationTypeID = $request->query->get('RegistrationType_ID');
        $registrationType = $this->getDoctrine()
            ->getRepository('AppBundle:Registrationtype')
            ->find($registrationTypeID);

        $registrationStatusID = $request->query->get('RegistrationStatus_ID');
        $registrationStatus = $this->getDoctrine()
            ->getRepository('AppBundle:Registrationstatus')
            ->find($registrationStatusID);

        $badgeTypeID = $request->query->get('BadgeType_ID');
        $badgeType = $this->getDoctrine()
            ->getRepository('AppBundle:Badgetype')
            ->find($badgeTypeID);

        if ($page <= 1 || !is_numeric($page)) {
            $page = 1;
        }
        $returnJson['page'] = $page;

        $returnJson = $this->get('repository_registration')->searchFromManageRegistrations(
            $searchText,
            $page,
            $registrationType,
            $registrationStatus,
            $badgeType
        );

        return new JsonResponse($returnJson);
        /*
        return new Response(
            '{"page":1,"count_total":"1","count_returned":1,"results":[{"Registration_ID":"17051","ConfirmationNumber":"65a32d4b005d0149","Email":"sjnandez@gmail.com","Year":"2018","Number":"H0001","Badge_Type":null,"FirstName":"Stephanie","LastName":"Hernandez","BadgeName":"Stephanie Hernandez","Reg_Status":"New","group":"","Volunteer":"","Newsletter":"X","is_adult":1,"is_minor":0,"is_sponsor":0,"is_comsponsor":0,"is_guest":0,"is_vendor":0,"is_staff":0,"is_exhibitor":0}]}'
        );
        */
    }

    public function registrationStatusListAction()
    {
        $registrationStatusList = [];

        $registrationStatuses = $this->get('repository_registrationstatus')->findAll();
        foreach ($registrationStatuses as $registrationStatus) {
            $id = $registrationStatus->getRegistrationstatusId();
            $registrationStatusList[] = [
                'id' => $id,
                'status' => $registrationStatus->getStatus(),
            ];
        }

        return $this->render('manage/registrationstatuslist.sub.html.twig', array('statusList' => $registrationStatusList));
    }

    public function registrationTypeListAction()
    {
        $registrationTypeList = [];

        $registrationTypes = $this->get('repository_registrationtype')->findAll();
        foreach ($registrationTypes as $registrationType) {
            $id = $registrationType->getRegistrationtypeId();
            $registrationTypeList[] = [
                'id' => $id,
                'type' => $registrationType->getName(),
            ];
        }

        return $this->render('manage/registrationtypelist.sub.html.twig', array('typeList' => $registrationTypeList));
    }

    public function badgeTypeListAction()
    {
        $badgeTypeList = [];

        $badgeTypes = $this->get('repository_badgetype')->findAll();
        foreach ($badgeTypes as $badgeType) {
            $id = $badgeType->getBadgetypeId();
            $badgeTypeList[] = [
                'id' => $id,
                'name' => $badgeType->getDescription(),
            ];
        }

        return $this->render('manage/badgetypelist.sub.html.twig', array('badgeTypeList' => $badgeTypeList));
    }
}
