<?php

// src/Controller/DefaultController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The Procces test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class DefaultController
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(CommonGroundService $commonGroundService, Request $request, ParameterBagInterface $params)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        if ($this->getUser()) {
            $person = $commonGroundService->getResource($this->getUser()->getPerson());
            $personUrl = $commonGroundService->cleanUrl(['component' => 'cc', 'type' => 'people', 'id' => $person['id']]);

            $employees = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'employees'], ['person' => $personUrl])['hydra:member'];

            if (!count($employees) > 0) {
                $employee = [];
                $employee['person'] = $personUrl;

                $commonGroundService->createResource($employee, ['component' => 'mrc', 'type' => 'employees']);

                $providers = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'providers'], ['type' => 'id-vault', 'application' => $params->get('app_id')])['hydra:member'];
                $provider = $providers[0];

                $users = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'users'], ['username' => $this->getUser()->getUsername()])['hydra:member'];
                $user = $users[0];

                $userUrl = $commonGroundService->cleanUrl(['component' => 'uc', 'type' => 'users', 'id' => $user['id']]);

                $authorizations = $commonGroundService->getResourceList(['component' => 'wac', 'type' => 'authorizations'], ['userUrl' => $userUrl, 'application' => '/applications/'.$provider['configuration']['app_id']])['hydra:member'];

//                if (count($authorizations) > 0) {
//                    $authorization = $authorizations[0];
//
//                    $dossier = [];
//                    $dossier['name'] = 'employee dossier';
//                    $dossier['description'] = 'employee dossier for '.$person['name'];
//                    $dossier['sso'] = $this->generateUrl('app_dashboard_index');
//                    $date = new \DateTime('now');
//                    $date->add(new \DateInterval('P2Y'));
//                    $dossier['expiryDate'] = $date->format('h:m Y-m-d');
//                    $dossier['goal'] = 'have access to employee dossier';
//                    $dossier['authorization'] = '/authorizations/'.$authorization['id'];
//
//                    $commonGroundService->createResource($dossier, ['component' => 'wac', 'type' => 'dossiers']);
//                }
            }
        }

        return $variables;
    }

    /**
     * @Route("/login")
     * @Template
     */
    public function loginAction(CommonGroundService $commonGroundService, Request $request)
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/register")
     * @Template
     */
    public function registerAction(CommonGroundService $commonGroundService, Request $request)
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/newsletter")
     * @Template
     */
    public function newsletterAction(Session $session, Request $request, CommonGroundService $commonGroundService, ParameterBagInterface $params, EventDispatcherInterface $dispatcher)
    {
        // TODO: use email used in form to subscribe to the newsletter?

        $session->set('backUrl', $request->query->get('backUrl'));

        $providers = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'providers'], ['type' => 'id-vault', 'application' => $params->get('app_id')])['hydra:member'];
        $provider = $providers[0];

        $redirect = $request->getUri();

        if (strpos($redirect, '?') == true) {
            $redirect = substr($redirect, 0, strpos($redirect, '?'));
        }

        if (isset($provider['configuration']['app_id']) && isset($provider['configuration']['secret'])) {
            $dev = '';
            if ($params->get('app_env') == 'dev') {
                $dev = 'dev.';
            }
            return $this->redirect('http://'.$dev.'id-vault.com/sendlist/authorize?client_id='.$provider['configuration']['app_id'].'&send_lists=8b929e53-1e16-4e59-a254-6af6b550bd08&redirect_uri='.$redirect);
        } else {
            return $this->render('500.html.twig');
        }
    }
}
