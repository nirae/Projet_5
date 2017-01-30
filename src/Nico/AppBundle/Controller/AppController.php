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
        $updateLink = $this->get('nico_app.circuit_manager')->getUpdateLink($id);
        $deleteLink = $this->get('nico_app.circuit_manager')->getDeleteLink($id);

        return array(
            'circuit' => $circuit,
            'updateLink' => $updateLink,
            'deleteLink' => $deleteLink
        );
    }

    public function confirmationAction($id, $name)
    {
        return $this->get('nico_app.circuit_manager')->confirmation($id, $name);
    }

    public function ajaxAction(Request $request)
    {
        return $this->get('nico_app.circuit_manager')->ajaxPost($request);
    }

    public function checkUpdateAction($id, $email)
    {
        return $this->get('nico_app.circuit_manager')->checkUpdateLink($id, $email);
    }

    /**
     * @Template("NicoAppBundle:App:update.html.twig")
     */
    public function pageUpdateAction($id, $email, $token, Request $request)
    {
        $form = $this->get('nico_app.circuit_manager')->pageUpdate($id, $email, $token, $request);

        return array('form' => $form);
    }

    public function checkDeleteAction($id, $email)
    {
        return $this->get('nico_app.circuit_manager')->checkDeleteLink($id, $email);
    }

    public function confirmationDeleteAction($id, $email, $token)
    {
        return $this->get('nico_app.circuit_manager')->confirmationDelete($id, $email, $token);
    }
}
