<?php declare(strict_types=1);

namespace AppBundle\Controller\Manage;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ManageController extends Controller
{
    /**
     * @Route("/manage/")
     *
     * @param Request $request
     * @return Response
     */
    public function listRegistrationsPage(Request $request)
    {
        $test = 'hi';
        // $_GET parameters
        if ($request->query->get('test')) {
            $test = $request->query->get('test');
        }

        $vars = [
            'current_RegistrationType_ID' => '',
            'current_RegistrationStatus_ID' => '',
            'current_BadgeType_ID' => '',
            'current_RegistrationType' => 'All',
            'current_RegistrationStatus' => 'All',
            'current_BadgeType' => 'All',
            'searchText' => '',
            'page' => '1',
        ];

        return $this->render('manage/manage.html.twig', $vars);
    }

    /**
     * @Route("/registrationlist")
     */
    public function tmp()
    {
        return new Response(
            '{"page":1,"count_total":"1","count_returned":1,"results":[{"Registration_ID":"17051","ConfirmationNumber":"65a32d4b005d0149","Email":"sjnandez@gmail.com","Year":"2018","Number":"H0001","Badge_Type":null,"FirstName":"Stephanie","LastName":"Hernandez","BadgeName":"Stephanie Hernandez","Reg_Status":"New","group":"","Volunteer":"","Newsletter":"X","is_adult":1,"is_minor":0,"is_sponsor":0,"is_comsponsor":0,"is_guest":0,"is_vendor":0,"is_staff":0,"is_exhibitor":0}]}'
        );
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
