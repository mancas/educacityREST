<?php

namespace EducacityREST\SynchronizeBundle\Controller;

use EducacityREST\ImageBundle\Entity\ImageUser;
use EducacityREST\ImageBundle\Util\FileHelper;
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
use Symfony\Component\Validator\Constraints\File;

class SynchronizeController extends FOSRestController
{
    /**
     * POST Route annotation.
     * @Post("/synchronize/file")
     * @View(serializerEnableMaxDepthChecks=true)
     */
    public function synchronizeFileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $image = new ImageUser();
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $userID = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->findOneById($userID);
        $em->persist($image);
        $em->flush();

        $directory = FileHelper::PATH . '/' . $image->getSubdirectory();

        $name =  basename($_FILES['uploaded_file']['name']);
        $targetFile = $directory . '/' .
            FileHelper::getFileNameFromId($image->getId(), $name);
        if (!is_dir($directory))
            mkdir($directory);
        if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $targetFile)) {
            $image->setImage($name);
            $user->addImage($image);
            $image->setUser($user);
            $image->setSynchronized(true);
            $em->persist($image);
            $em->persist($user);
            $em->flush();
            $jsonResponse = json_encode(array('code' => 200));
            $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(200);
            return $response;
        } else {
            $em->remove($image);
            $em->flush();
        }
        $jsonResponse = json_encode(array('code' => 400));
        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(400);
        return $response;
    }

    /**
     * POST Route annotation.
     * @Post("/synchronize/profile")
     * @View(serializerEnableMaxDepthChecks=true)
     */
    public function synchronizeProfileAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        if (false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
        $userID = $this->get('security.context')->getToken()->getUser()->getId();
        $user = $em->getRepository('UserBundle:User')->findOneById($userID);
ldd($user);
        $name = $request->request->get('name');
        $publicProfile = $request->request->get('public_profile');
        if ($publicProfile == 1) {
            $publicProfile = true;
        } else {
            $publicProfile = false;
        }
        $user->setName($name);
        $user->setPublic($publicProfile);
        $em->persist($user);
        $em->flush();

        $jsonResponse = json_encode(array('code' => 200, 'name' => $name, 'public' => $publicProfile));
        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(200);

        return $response;
    }
}
