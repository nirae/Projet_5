<?php

namespace Nico\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AppController extends Controller
{
    /**
     * @Template("NicoAppBundle:App:index.html.twig")
     */
    public function indexAction()
    {
        return;
    }

    /**
     * @Template("NicoAppBundle:App:add.html.twig")
     */
    public function addAction(Request $request)
    {
        $form = $this->get('nico_app.circuit_manager')->addForm($request);

        return array('form' => $form);
    }

    /**
     * @Template("NicoAppBundle:App:fiche.html.twig")
     */
    public function ficheAction($id)
    {
        $circuit = $this->get('nico_app.circuit_manager')->displayCircuit($id);

        return array('circuit' => $circuit);
    }

    public function confirmationAction($id, $name)
    {
        return $this->get('nico_app.circuit_manager')->confirmation($id, $name);
    }

    public function ajaxAction(Request $request)
    {
        return $this->get('nico_app.circuit_manager')->ajaxPost($request);
    }
}
