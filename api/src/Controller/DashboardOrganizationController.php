<?php

// src/Controller/DashboardController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * The DashboardController test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class DashboardController
 *
 * @Route("/dashboard/organization")
 */
class DashboardOrganizationController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        return $variables;
    }

    /**
     * @Route("/tutorials")
     * @Template
     */
    public function tutorialsAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (empty($this->getUser()->getOrganization())) {
            return $this->redirect($this->generateUrl('app_default_organization').'?backUrl='.$this->generateUrl('app_dashboardorganization_tutorials'));
        }

        $variables = [];

        $variables['addPath'] = 'app_dashboardorganization_tutorial';
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        if ($this->getUser() && $this->getUser()->getOrganization()) {
            $variables['query'][] = ['organization' => $this->getUser()->getOrganization()];
            // Get resource tutorials (known as cources component side)
            $variables['tutorials'] = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'courses'], ['organization' => $commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $commonGroundService->getUuidFromUrl($this->getUser()->getOrganization())])])['hydra:member'];
        }

        return $variables;
    }

    /**
     * @Route("/tutorials/{id}")
     * @Template
     */
    public function tutorialAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];
        $variables['additionalType'] = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'activities'])['hydra:member'];
        $variables['organizations'] = $commonGroundService->getResourceList(['component' => 'wrc', 'type' => 'organizations'])['hydra:member'];
        $variables['activities'] = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'activities'])['hydra:member'];
        $variables['requirements'] = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'courses'])['hydra:member'];
        $variables['skills'] = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'skills'])['hydra:member'];
        $variables['competences'] = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'competences'])['hydra:member'];

        if ($this->getUser()->getOrganization()) {
            $organization = $commonGroundService->getResource($this->getUser()->getOrganization());
            $organizationUrl = $commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $organization['id']]);
            $variables['products'] = $commonGroundService->getResourceList(['component' => 'pdc', 'type' => 'products'], ['sourceOrganization' => $organizationUrl])['hydra:member'];
        } else {
            $variables['products'] = [];
        }

        if ($id != 'new') {
            // Get resource tutorial (known as course component side)
            $variables['tutorial'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses', 'id' => $id]);
            $variables['participants'] = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants'], ['course.id' => $id])['hydra:member'];
        } else {
            $variables['tutorial'] = ['id' => 'new'];
            $variables['tutorial']['name'] = 'new tutorial';
        }
        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            $resource['organization'] = $organizationUrl;
            $variables['tutorial'] = $commonGroundService->saveResource($resource, ['component' => 'edu', 'type' => 'courses']);

            return $this->redirect($this->generateUrl('app_dashboardorganization_tutorials'));
        }

        return $variables;
    }

    /**
     * @Route("/internships")
     * @Template
     */
    public function internshipsAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (empty($this->getUser()->getOrganization())) {
            return $this->redirect($this->generateUrl('app_default_organization').'?backUrl='.$this->generateUrl('app_dashboardorganization_internships'));
        }

        $variables = [];

        $variables['addPath'] = 'app_dashboardorganization_internship';
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        // Get resources Interschips
        // TODO:make sure only the internships of the correct organization(s?) are loaded
        $variables['internships'] = $commonGroundService->getResource(['component' => 'mrc', 'type' => 'job_postings'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/internships/{id}")
     * @Template
     */
    public function internshipAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        //Get resources Organizations
        // TODO:this should be all organizations of a specific wrc.contact -> cc/organization.type (Participant in cc/StageFixtures)
        // TODO:or maybe this shouldn't be here at all, this is only used for selecting the hiringOrganization, but the hiringOrganization might just be set without user input
        $variables['organizations'] = $commonGroundService->getResource(['component' => 'wrc', 'type' => 'organizations'])['hydra:member']; // , $variables['query']

        // Get resource Interschip
        if ($id != 'new') {
            $variables['internship'] = $commonGroundService->getResource(['component' => 'mrc', 'type' => 'job_postings', 'id' => $id]);

            //get applications from current job_posting
            $variables['applications'] = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'applications'], ['jobPosting.id' => $id])['hydra:member'];
        } else {
            $variables['internship'] = ['id' => 'new'];
            $variables['internship']['name'] = 'new internship';
        }
        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();
            // Add the post data to the already aquired resource data
//            $resource = array_merge($variables['internship'], $resource);

            // Make sure there is no invalid input for validThrough
            if (empty($resource['validThrough'])) {
                unset($resource['validThrough']);
            }

            // Make sure there is no invalid input for standardHours and baseSalary
            $resource['standardHours'] = (int) $resource['standardHours'];
            if (empty($resource['baseSalary'])) {
                unset($resource['baseSalary']);
            } else {
                $resource['baseSalary'] = (int) $resource['baseSalary'];
            }

            // Update or create to the commonground component
            $variables['internship'] = $commonGroundService->saveResource($resource, ['component' => 'mrc', 'type' => 'job_postings']);

            return $this->redirect($this->generateUrl('app_dashboardorganization_internships'));
        }

        return $variables;
    }

    /**
     * @Route("/challenges")
     * @Template
     */
    public function challengesAction(Request $request, CommonGroundService $commonGroundService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (empty($this->getUser()->getOrganization())) {
            return $this->redirect($this->generateUrl('app_default_organization').'?backUrl='.$this->generateUrl('app_dashboardorganization_challenges'));
        }

        $variables = [];

        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        // Get resource challenges (known as tender component side)
        if ($this->getUser()->getOrganization()) {
            $organization = $commonGroundService->getResource($this->getUser()->getOrganization());
            $organizationUrl = $commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $organization['id']]);
            $variables['challenges'] = $commonGroundService->getResourceList(['component' => 'chrc', 'type' => 'tenders'], ['submitter' => $organizationUrl])['hydra:member'];
        } else {
            $variables['challenges'] = [];
        }

        $variables['addPath'] = 'app_dashboardorganization_challenge';

        return $variables;
    }

    /**
     * @Route("/challenges/{id}")
     * @Template
     */
    public function challengeAction(Request $request, CommonGroundService $commonGroundService, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        if ($this->getUser()->getOrganization()) {
            $variables['organization'] = $commonGroundService->getResource($this->getUser()->getOrganization());
        }

        $variables['tutorials'] = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'courses'])['hydra:member'];

        if ($id != 'new') {
            // Get resource challenges (known as tender component side)
            $variables['challenge'] = $commonGroundService->getResource(['component' => 'chrc', 'type' => 'tenders', 'id' => $id]);
            $variables['proposals'] = $commonGroundService->getResourceList(['component' => 'chrc', 'type' => 'proposals'], ['tender.id' => $id])['hydra:member'];
        } else {
            $variables['challenge'] = ['id' => 'new'];
        }

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            // Add the post data to the already aquired resource data
            $resource = array_merge($variables['challenge'], $resource);

            //lets see if we have child objects in an existing tender if so we set those to the @id of the object
            //@todo function to do this
            if (isset($resource['stages'])) {
                foreach ($resource['stages'] as &$stage) {
                    $stage = '/tender_stages/'.$stage['id'];
                }
            }

            // Update to the commonground component
            $variables['challenge'] = $commonGroundService->saveResource($resource, ['component' => 'chrc', 'type' => 'tenders']);

            return $this->redirect($this->generateUrl('app_dashboardorganization_challenges'));
        }

        return $variables;
    }

    /**
     * @Route("/products")
     * @Template
     */
    public function productsAction(Request $request, CommonGroundService $commonGroundService)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (empty($this->getUser()->getOrganization())) {
            return $this->redirect($this->generateUrl('app_default_organization').'?backUrl='.$this->generateUrl('app_dashboardorganization_products'));
        }

        $variables = [];

        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        // Get resource challenges (known as tender component side)
        if ($this->getUser()->getOrganization()) {
            $organization = $commonGroundService->getResource($this->getUser()->getOrganization());
            $organizationUrl = $commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $organization['id']]);
            $variables['products'] = $commonGroundService->getResourceList(['component' => 'pdc', 'type' => 'products'], ['sourceOrganization' => $organizationUrl])['hydra:member'];
        } else {
            $variables['products'] = [];
        }

        $variables['addPath'] = 'app_dashboardorganization_product';

        return $variables;
    }

    /**
     * @Route("/products/{id}")
     * @Template
     */
    public function productAction(Request $request, CommonGroundService $commonGroundService, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        if ($this->getUser()->getOrganization()) {
            $variables['organization'] = $commonGroundService->getResource($this->getUser()->getOrganization());
        }

        if ($id != 'new') {
            // Get resource challenges (known as tender component side)
            $variables['product'] = $commonGroundService->getResource(['component' => 'pdc', 'type' => 'products', 'id' => $id]);
        } else {
            $variables['product'] = ['id' => 'new'];
        }

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            // Add the post data to the already aquired resource data
            $resource = array_merge($variables['product'], $resource);

            $organizationUrl = $commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $variables['organization']['id']]);

            $resource['sourceOrganization'] = $organizationUrl;
            $resource['requiresAppointment'] = false;

            // Update to the commonground component
            $variables['product'] = $commonGroundService->saveResource($resource, ['component' => 'pdc', 'type' => 'products']);

            return $this->redirect($this->generateUrl('app_dashboardorganization_products'));
        }

        return $variables;
    }

    /**
     * @Route("/teams")
     * @Template
     */
    public function teamsAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        $variables['teams'] = [];

        return $variables;
    }

    /**
     * @Route("/teams/{id}")
     * @Template
     */
    public function teamAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/competences")
     * @Template
     */
    public function competencesAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        $variables['competences'] = [];

        return $variables;
    }

    /**
     * @Route("/competences/{id}")
     * @Template
     */
    public function competenceAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/settings")
     * @Template
     */
    public function settingsAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        if ($this->getUser()) {
            if ($commonGroundService->isResource($this->getUser()->getOrganization())) {
                $variables['organization'] = $commonGroundService->getResource($this->getUser()->getOrganization());
            }

            if (isset($variables['organization']['contact']) and $commonGroundService->isResource($variables['organization']['contact'])) {
                $variables['organizationContact'] = $commonGroundService->getResource($variables['organization']['contact']);
                $variables['organizationContact'] = $commonGroundService->getResource(['component' => 'cc', 'type' => 'organizations', 'id' => $variables['organizationContact']['id']]);
            }
        }

        if ($request->isMethod('POST') && $request->get('updateInfo')) {
            $name = $request->get('name');

            // Update (or create) the wrc/organization of this user
            if (isset($variables['organization'])) {
                $organization = $variables['organization'];
            }
            $organization['name'] = $name;
            $organization['description'] = $name;
            $organization['chamberOfComerce'] = $request->get('chamberOfComerce');
            $organization['rsin'] = $request->get('rsin');

            // Update (or create) the cc/organization of this user
            if (isset($variables['organizationContact'])) {
                $organizationContact = $variables['organizationContact'];
            }
            $organizationContact['name'] = $name;

            // email
            if (isset($organizationContact['emails'][0])) {
                $email = $organizationContact['emails'][0];
            } else {
                $email = [];
            }
            $email['name'] = 'email for '.$name;
            $email['email'] = $request->get('email');
            if (isset($email['id'])) {
                if (empty($email['email'])) {
                    $commonGroundService->deleteResource($email, ['component' => 'cc', 'type' => 'emails']);
                    unset($organizationContact['emails'][0]);
                } else {
                    $commonGroundService->saveResource($email, ['component' => 'cc', 'type' => 'emails']);
                    $organizationContact['emails'][0] = '/emails/'.$email['id'];
                }
            } elseif (isset($email['email'])) {
                $organizationContact['emails'][0] = $email;
            }

            // telephone
            if (isset($organizationContact['telephones'][0])) {
                $telephone = $organizationContact['telephones'][0];
            } else {
                $telephone = [];
            }
            $telephone['name'] = 'telephone for '.$name;
            $telephone['telephone'] = $request->get('telephone');
            if (isset($telephone['id'])) {
                if (empty($telephone['telephone'])) {
                    $commonGroundService->deleteResource($telephone, ['component' => 'cc', 'type' => 'telephones']);
                    unset($organizationContact['telephones'][0]);
                } else {
                    $commonGroundService->saveResource($telephone, ['component' => 'cc', 'type' => 'telephones']);
                    $organizationContact['telephones'][0] = '/telephones/'.$telephone['id'];
                }
            } elseif (isset($telephone['telephone'])) {
                $organizationContact['telephones'][0] = $telephone;
            }

            // $address
            if (isset($organizationContact['adresses'][0])) {
                $address = $organizationContact['adresses'][0];
            } else {
                $address = [];
            }
            $address['name'] = 'address for '.$name;
            $address['street'] = $request->get('street');
            $address['houseNumber'] = $request->get('houseNumber');
            $address['houseNumberSuffix'] = $request->get('houseNumberSuffix');
            $address['postalCode'] = $request->get('postalCode');
            $address['locality'] = $request->get('locality');
            if (isset($address['id'])) {
                $commonGroundService->saveResource($address, ['component' => 'cc', 'type' => 'addresses']);
                $organizationContact['adresses'][0] = '/addresses/'.$address['id'];
            } else {
                $organizationContact['adresses'][0] = $address;
            }

            // Socials
            if (isset($organizationContact['socials'][0])) {
                $twitter = $organizationContact['socials'][0];
            } else {
                $twitter = [];
            }
            $twitter['name'] = 'Twitter of '.$name;
            $twitter['description'] = 'Twitter of '.$name;
            $twitter['type'] = 'twitter';
            $twitter['url'] = $request->get('twitter');
            if (isset($twitter['id'])) {
                $commonGroundService->saveResource($twitter, ['component' => 'cc', 'type' => 'socials']);
                $organizationContact['socials'][0] = '/socials/'.$twitter['id'];
            } else {
                $organizationContact['socials'][0] = $twitter;
            }

            if (isset($organizationContact['socials'][1])) {
                $facebook = $organizationContact['socials'][1];
            } else {
                $facebook = [];
            }
            $facebook['name'] = 'Facebook of '.$name;
            $facebook['description'] = 'Facebook of '.$name;
            $facebook['type'] = 'facebook';
            $facebook['url'] = $request->get('facebook');
            if (isset($facebook['id'])) {
                $commonGroundService->saveResource($facebook, ['component' => 'cc', 'type' => 'socials']);
                $organizationContact['socials'][1] = '/socials/'.$facebook['id'];
            } else {
                $organizationContact['socials'][1] = $facebook;
            }

            if (isset($organizationContact['socials'][2])) {
                $instagram = $organizationContact['socials'][2];
            } else {
                $instagram = [];
            }
            $instagram['name'] = 'Instagram of '.$name;
            $instagram['description'] = 'Instagram of '.$name;
            $instagram['type'] = 'instagram';
            $instagram['url'] = $request->get('instagram');
            if (isset($instagram['id'])) {
                $commonGroundService->saveResource($instagram, ['component' => 'cc', 'type' => 'socials']);
                $organizationContact['socials'][2] = '/socials/'.$instagram['id'];
            } else {
                $organizationContact['socials'][2] = $instagram;
            }

            if (isset($organizationContact['socials'][3])) {
                $linkedin = $organizationContact['socials'][3];
            } else {
                $linkedin = [];
            }
            $linkedin['name'] = 'Linkedin of '.$name;
            $linkedin['description'] = 'Linkedin of '.$name;
            $linkedin['type'] = 'linkedin';
            $linkedin['url'] = $request->get('linkedin');
            if (isset($linkedin['id'])) {
                $commonGroundService->saveResource($linkedin, ['component' => 'cc', 'type' => 'socials']);
                $organizationContact['socials'][3] = '/socials/'.$linkedin['id'];
            } else {
                $organizationContact['socials'][3] = $linkedin;
            }

            if (isset($organizationContact['socials'][4])) {
                $github = $organizationContact['socials'][4];
            } else {
                $github = [];
            }
            $github['name'] = 'Github of '.$name;
            $github['description'] = 'Github of '.$name;
            $github['type'] = 'github';
            $github['url'] = $request->get('github');
            if (isset($github['id'])) {
                $commonGroundService->saveResource($github, ['component' => 'cc', 'type' => 'socials']);
                $organizationContact['socials'][4] = '/socials/'.$github['id'];
            } else {
                $organizationContact['socials'][4] = $github;
            }

            if (isset($organizationContact['socials'][5])) {
                $website = $organizationContact['socials'][5];
            } else {
                $website = [];
            }
            $website['name'] = 'Website of '.$name;
            $website['description'] = 'Website of '.$name;
            $website['type'] = 'website';
            $website['url'] = $request->get('website');
            if (isset($website['id'])) {
                $commonGroundService->saveResource($website, ['component' => 'cc', 'type' => 'socials']);
                $organizationContact['socials'][5] = '/socials/'.$website['id'];
            } else {
                $organizationContact['socials'][5] = $website;
            }

            $organizationContact = $commonGroundService->saveResource($organizationContact, ['component' => 'cc', 'type' => 'organizations']);

            $organization['contact'] = $commonGroundService->cleanUrl(['component' => 'cc', 'type' => 'organizations', 'id' => $organizationContact['id']]);
            $organization = $commonGroundService->saveResource($organization, ['component' => 'wrc', 'type' => 'organizations']);

            // If this user has no organization the user.organization should be set to this $organization?
            $users = $commonGroundService->getResourceList(['component' => 'uc', 'type' => 'users'], ['username' => $this->getUser()->getUsername()])['hydra:member'];
            if (count($users) > 0) {
                $user = $users[0];

                if (!$commonGroundService->isResource($this->getUser()->getOrganization()) or !isset($user['organization'])) {
                    $user['organization'] = $commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $organization['id']]);
                    foreach ($user['userGroups'] as &$userGroup) {
                        $userGroup = '/groups/'.$userGroup['id'];
                    }
                    $commonGroundService->updateResource($user);
                }
            }

            return $this->redirect($this->generateUrl('app_dashboardorganization_settings'));
        }

        return $variables;
    }

    /**
     * @Route("/participants")
     * @Template
     */
    public function participantsAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        if ($request->isMethod('POST')) {
            $resource = $request->request->all();
            $participation = $commonGroundService->getResource($resource['id']);

            $participation['status'] = $resource['status'];
            unset($participation['participantGroup']);
            unset($participation['course']);
            unset($participation['program']);
            if ($participation['status'] == 'accepted') {
                $participation['dateOfAcceptance'] = new Date('today');
            } else {
                $participation['dateOfAcceptance'] = null;
            }
            $participation = $commonGroundService->saveResource($participation, $participation['@id']);
        }

        $allParticipants = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants'])['hydra:member'];

        // Some code to get the right participants of this organization
        $participantIds = [];
        if (isset($allParticipants) && count($allParticipants) > 0) {
            foreach ($allParticipants as $part) {
                if (!in_array($part['id'], $participantIds)) {
                    if (isset($part['course']) &&
                        isset($part['course']['organization']) &&
                        $part['course']['organization'] == $this->getUser()->getOrganization()) {
                        $variables['participants'][] = $part;
                    } elseif (isset($part['program']) &&
                        isset($part['program']['organization']) &&
                        $part['program']['organization'] == $this->getUser()->getOrganization()) {
                        $variables['participants'][] = $part;
                    }
                    $participantIds[] = $part['id'];
                }
            }
        }

        return $variables;
    }

    /**
     * @Route("/invoices")
     * @Template
     */
    public function invoicesAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        if (!empty($this->getUser()->getOrganization())) {
            $organization = $commonGroundService->getResource($this->getUser()->getOrganization());
            $organizationUrl = $commonGroundService->cleanUrl(['component' => 'wrc', 'type' => 'organizations', 'id' => $organization['id']]);
            $variables['invoices'] = $commonGroundService->getResourceList(['component' => 'bc', 'type' => 'invoices'], ['customer' => $organizationUrl])['hydra:member'];
        }

        return $variables;
    }

    /**
     * @Route("/invoice/{id}")
     * @Template
     */
    public function invoiceAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        $variables['invoice'] = $commonGroundService->getResource(['component' => 'bc', 'type' => 'invoices', 'id' => $id]);

        /*@todo make payment process*/

        return $variables;
    }

    /**
     * @Route("/conduction")
     * @Template
     */
    public function conductionAction(CommonGroundService $commonGroundService, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $variables = [];

        $variables['challenges'] = $commonGroundService->getResourceList(['component' => 'chrc', 'type' => 'tenders'])['hydra:member'];
        $variables['stages'] = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'job_postings'])['hydra:member'];

        if (count($variables['stages']) > 0) {
            foreach ($variables['stages'] as &$stage) {
                $appliedFor = false;
                $applications = $commonGroundService->getResourceList(['component' => 'mrc', 'type' => 'applications'], ['jobPosting.id' => $stage['id']])['hydra:member'];
                if (count($applications) > 0) {
                    $appliedFor = true;
                }

                $stage['appliedFor'] = $appliedFor;
            }
        }

        return $variables;
    }
}
