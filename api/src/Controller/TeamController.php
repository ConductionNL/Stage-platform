<?php

// src/Controller/TeamController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * The TeamController test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class TeamController
 *
 * @Route("/teams")
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(CommonGroundService $commonGroundService, Request $request)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        return $variables;
    }

    /**
     * @Route("/{id}")
     * @Template
     */
    public function teamAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $variables = [];

        // Get resource Interschip
        $variables['team'] = $commonGroundService->getResource(['component' => 'mrc', 'type' => 'job_postings','id'=>$id]);

        return $variables;
    }

}
