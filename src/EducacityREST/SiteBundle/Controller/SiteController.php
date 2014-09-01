<?php

namespace EducacityREST\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SiteController extends Controller
{
    public function getSiteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $site = $em->getRepository('SiteBundle:Site')->findOneById($id);
        $jsonResponse = json_encode(array('id' => $site->getId(), 'title' => $site->getName(),
            'latitude' => $site->getLatitude(),
            'longitude' => $site->getLongitude(),
            'information' => $site->getInformation()));
        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');
    }

    public function getJSON($sites)
    {
        $result = array();
        foreach ($sites as $site) {
            $result[] = array('id' => $site->getId(), 'title' => $site->getName(),
                'latitude' => $site->getLatitude(),
                'longitude' => $site->getLongitude());
        }

        return $result;
    }

    public function getSitesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $sites = $em->getRepository('SiteBundle:Site')->findAll();
        $jsonResponse = json_encode($this->getJSON($sites));
        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
