<?php

// src/Controller/EducationController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The EducationController test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class EducationController
 *
 * @Route("/education")
 */
class EducationController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction()
    {
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
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        // Get resource tutorials (known as cources component side)
        $variables['tutorials'] = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'courses'], $variables['query'])['hydra:member'];
        $variables['types'] = [];
        foreach ($variables['tutorials'] as $tutorial) {
            if (key_exists('additionalType', $tutorial) && $tutorial['additionalType'] != null && !in_array(strtolower($tutorial['additionalType']), $variables['types'])) {
                $variables['types'][] = strtolower($tutorial['additionalType']);
            }
        }

        return $variables;
    }

    /**
     * @Route("/tutorials/{id}")
     * @Template
     */
    public function tutorialAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $variables = [];

        // Get resource tutorial
        $variables['tutorial'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses', 'id' => $id]);

        //  Getting the participant @todo this needs to be more foolproof and part of a service
        if ($this->getUser()) {
            $participants = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants', ['person'=> $this->getUser()->getPerson()]])['hydra:member'];
        } else {
            $participants = $commonGroundService->getResourceList(['component' => 'edu', 'type' => 'participants', ['person'=> 'https://dev.zuid-drecht.nl/api/v1/cc/people/d961291d-f5c1-46f4-8b4a-6abb41df88db']])['hydra:member'];
        }

        $variables['participant'] = $participants[0];

        // Lets see if there is a post to procces
        if ($request->isMethod('POST')) {
            $resource = $request->request->all();

            // Fallback
            if (!array_key_exists('courses', $variables['participant'])) {
                $variables['participant']['courses'] = [];
            }

            $variables['participant']['courses'][] = '/courses/'.$variables['tutorial']['id'];
            $variables['participant'] = $commonGroundService->saveResource($variables['participant'], ['component' => 'edu', 'type' => 'participants', 'id' => $variables['participant']['id']]);
        }

        // lets see if the participant is in this course
        $variables['registered'] = false;
        foreach ($variables['participant']['courses'] as $tempCourse) {
            if ($tempCourse['id'] == $id) {
                $variables['registered'] = true;
            }
        }

        return $variables;
    }

    /**
     * @Route("/programs")
     * @Template
     */
    public function programsAction(CommonGroundService $commonGroundService, Request $request)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        // Get resources programs
        $variables['programs'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'programs'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/programs/{id}")
     * @Template
     */
    public function programAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $variables = [];

        // Get resource program
        $variables['tutorial'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'programs', 'id' => $id]);

        return $variables;
    }

    /**
     * @Route("/courses")
     * @Template
     */
    public function coursesAction(CommonGroundService $commonGroundService, Request $request)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        // Get resources programs
        $variables['programs'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses'], $variables['query'])['hydra:member'];

        return $variables;
    }

    /**
     * @Route("/programs/{id}")
     * @Template
     */
    public function courseAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $variables = [];

        // Get resource program
        $variables['tutorial'] = $commonGroundService->getResource(['component' => 'edu', 'type' => 'courses', 'id' => $id]);

        return $variables;
    }
}
