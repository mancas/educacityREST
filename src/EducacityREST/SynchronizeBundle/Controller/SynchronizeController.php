<?php

namespace EducacityREST\SynchronizeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Options;

class SynchronizeController extends FOSRestController
{
    /**
     * POST Route annotation.
     * @Post("/synchronize")
     * @View(serializerEnableMaxDepthChecks=true)
     */
    public function synchronizeAction(Request $request)
    {
        ldd($request, $_FILES);
        return $this->render('SynchronizeBundle:Default:index.html.twig');
    }
}
