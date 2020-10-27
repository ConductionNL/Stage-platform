<?php

// src/Controller/ChallangeController.php

namespace App\Controller;

use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * The ChallangeController test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class ChallangeController
 *
 * @Route("/challanges")
 */
class ChallangeController extends AbstractController
{
    /**
     * @Route("/")
     * @Template
     */
    public function indexAction(CommonGroundService $commonGroundService, Request $request)
    {
        // On an index route we might want to filter based on user input
        $variables['query'] = array_merge($request->query->all(), $variables['post'] = $request->request->all());

        // Get resource challanges (known as tender component side)
        $variables['challanges'] = $commonGroundService->getResource(['component' => 'chrc', 'type' => 'tenders'], $variables['query'])['hydra:member'];

        return $variables;
    }


    /**
     * @Route("/{id}")
     * @Template
     */
    public function challangeAction(CommonGroundService $commonGroundService, Request $request, $id)
    {
        $variables = [];

        // Get resource challanges (known as tender component side)
        $variables['challange'] = $commonGroundService->getResource(['component' => 'chrc', 'type' => 'tenders', 'id' => $id]);

        return $variables;
    }
}
