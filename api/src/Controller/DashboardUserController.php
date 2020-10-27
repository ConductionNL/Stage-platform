<?php

// src/Controller/DashboardController.php

namespace App\Controller;

//use App\Service\RequestService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The DashboardController test handles any calls that have not been picked up by another test, and wel try to handle the slug based against the wrc.
 *
 * Class DashboardController
 *
 * @Route("/dashboard/user")
 */
class DashboardUserController extends AbstractController
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
     * @Route("/tutorials")
     * @Template
     */
    public function tutorialsAction()
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/tutorials/{id}")
     * @Template
     */
    public function tutorialAction()
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/internships")
     * @Template
     */
    public function internshipsAction()
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/internships/{id}")
     * @Template
     */
    public function internshipAction()
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/challanges")
     * @Template
     */
    public function challangesAction()
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/challanges/{id}")
     * @Template
     */
    public function challangeAction()
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/teams")
     * @Template
     */
    public function teamsAction()
    {
        $variables = [];

        return $variables;
    }

    /**
     * @Route("/teams/{id}")
     * @Template
     */
    public function teamAction()
    {
        $variables = [];

        return $variables;
    }
}
