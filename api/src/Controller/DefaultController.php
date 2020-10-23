<?php


namespace App\Controller;

use Conduction\CommonGroundBundle\Service\ApplicationService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * The Default controller handles any calls for ....
 *
 * Class DefaultController
 *
 * @Route("/")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
       $variable = $this->container;

       return $variable;
    }

    /**
     * @Route("/challenges")
     * @Template
     */
    public function challengesAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/challenges/{id}")
     * @Template
     */
    public function challengeAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/challengeplaatsen")
     * @Template
     */
    public function challengeplaatsenAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/new-pitch")
     * @Template
     */
    public function newpitchAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/pitches")
     * @Template
     */
    public function pitchesAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/pitches/{id}")
     * @Template
     */
    public function pitchAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/proposals/{id}")
     * @Template
     */
    public function proposalAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/deals/{id}")
     * @Template
     */
    public function dealAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/questions/{id}")
     * @Template
     */
    public function questionAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/courses")
     * @Template
     */
    public function coursesAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/courses/{id}")
     * @Template
     */
    public function courseAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/activities/{id}")
     * @Template
     */
    public function activityAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/students")
     * @Template
     */
    public function studentsAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/students/{id}")
     * @Template
     */
    public function studentAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/internships")
     * @Template
     */
    public function internshipsAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/internships/{id}")
     * @Template
     */
    public function internshipAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/stageplaatsen")
     * @Template
     */
    public function stageplaatsenAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/teams")
     * @Template
     */
    public function teamsAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/teams/{id}")
     * @Template
     */
    public function teamAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/tests")
     * @Template
     */
    public function testsAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/tests/{id}")
     * @Template
     */
    public function testAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/overview")
     * @Template
     */
    public function overviewAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/organizations")
     * @Template
     */
    public function organizationsAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/organizations/{id}")
     * @Template
     */
    public function organizationAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/programs")
     * @Template
     */
    public function programsAction()
    {
        $variable = [];

        return $variable;
    }

    /**
     * @Route("/programs/{id}")
     * @Template
     */
    public function programAction()
    {
        $variable = [];

        return $variable;
    }

}
