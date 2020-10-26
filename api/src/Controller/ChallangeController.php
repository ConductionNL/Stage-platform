<?php

// src/Controller/ChallangeController.php

namespace App\Controller;

//use App\Service\RequestService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function indexAction()
    {
        $variables = [];

        return $variables;
    }


    /**
     * @Route("/{id}")
     * @Template
     */
    public function challangeAction($id)
    {
        $variables = [];

        return $variables;
    }
}
