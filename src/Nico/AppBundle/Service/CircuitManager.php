<?php

namespace Nico\AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Nico\AppBundle\Entity\Circuit;
use Nico\AppBundle\Form\CircuitType;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactory;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CircuitManager
{

    private $em;
    private $formfactory;
    private $mailer;
    private $templating;
    private $session;
    private $router;

        public function __construct(
            EntityManager $em,
            FormFactory $formfactory,
            \Swift_Mailer $mailer,
            TwigEngine $templating,
            Session $session,
            $router
        )
        {
            $this->em = $em;
            $this->formfactory = $formfactory;
            $this->mailer = $mailer;
            $this->templating = $templating;
            $this->session = $session;
            $this->router = $router;
        }


        public function addForm(Request $request)
        {
            $circuit = new Circuit();
            $form = $this->formfactory->create(CircuitType::class, $circuit);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // Flush
                $this->em->persist($circuit);
                $this->em->flush();
                // Flash message
                $this->session->getFlashBag()->add('notice', 'Ajouté! Vous allez recevoir un email permettant de confirmer l\'ajout de votre circuit');
                // Création lien de confirmation
                $link = $this->router->generate(
                    'nico_app_confirmation',
                    array(
                        'id' => $circuit->getId(),
                        'name' => $circuit->getName(),
                    ),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                // Envoyer mail de confirmation
                $message = \Swift_Message::newInstance()
                ->setSubject('Confirmation d\'ajout de votre circuit')
                ->setFrom('monspot@nicolasdubouilh.fr')
                ->setTo($circuit->getOwner()->getEmail())
                ->setBody(
                    $this->templating->render('NicoAppBundle:Email:confirmation.html.twig', array(
                        'link' => $link,
                        'circuit' => $circuit,
                    )),
                    'text/html'
                );
                // Envoi du message
                $this->mailer->send($message);
            }

            return $form->createView();
        }

        public function displayCircuit($id)
        {
            $circuit = $this->em->getRepository("NicoAppBundle:Circuit")->find($id);

            if (!$circuit->getIsValid()) {
                throw new NotFoundHttpException('Ce circuit n\' pas été validé par son responsable');
            }

            return $circuit;
        }

        public function confirmation($id, $name)
        {
            //Récupère le circuit
            $circuit = $this->em->getRepository("NicoAppBundle:Circuit")->find($id);
            // Si le circuit est déjà activé
            if ($circuit->getIsValid() === true) {
                // Flash message
                $this->session->getFlashBag()->add('notice', 'Votre circuit à déjà été activé !');
                // Redirection
                $response = new RedirectResponse('/');
                $response->send();
            } else {
                // Si le circuit correspond bien au lien
                if ($circuit->getName() == $name) {
                    // Activation
                    $circuit->activate();
                    // Flush
                    $this->em->persist($circuit);
                    $this->em->flush();
                    // Flash message
                    $this->session->getFlashBag()->add('notice', 'Votre circuit a bien été activé, il apparait désormais dans la liste');
                    // Redirection
                    $response = new RedirectResponse('/');
                    $response->send();
                } else {
                    // Flash message
                    $this->session->getFlashBag()->add('notice', 'Erreur lors de l\'activation du circuit');
                    // Redirection
                    $response = new RedirectResponse('/');
                    $response->send();
                }
            }
        }

        public function ajaxPost(Request $request)
        {
            if ($request->isMethod('POST')) {
                // Récupère le status envoyé via ajax
                $status = $request->get('status');
                if ($status === 'load') {
                    //Récupère les circuits
                    $repository = $this->em->getRepository('NicoAppBundle:Circuit');
                    $circuits = $repository->findBy(array('isValid' => true));
                    // Si il n'y a pas de circuits
                    if (count($circuits) === 0) {
                        return new JsonResponse(array(
                            'rep' => false,
                        ));
                    }
                    // Si c'est bon
                    $listCircuits = array();
                    foreach ($circuits as $c) {
                        $listCircuits[] = array(
                            'id' => $c->getId(),
                            'name' => $c->getName(),
                            'hours' => $c->getHours(),
                            'licence' => $c->getLicence(),
                            'phone' => $c->getOwner()->getPhoneNumber(),
                            'email' => $c->getOwner()->getEmail(),
                            'latitude' => $c->getLatitude(),
                            'longitude' => $c->getLongitude(),
                        );
                    }

                    return new JsonResponse(array(
                        'rep' => $listCircuits,
                    ));
                } else {
                    return new JsonResponse(array(
                        'rep' => false,
                    ));
                }
            }
        }
    }
