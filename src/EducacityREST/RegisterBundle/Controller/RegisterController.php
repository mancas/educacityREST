<?php
namespace EducacityREST\RegisterBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Controller\Annotations\View;
use EducacityREST\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use EducacityREST\UserBundle\Form\AppUserType;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends FOSRestController{

    public function postRegisterAction(Request $request)
    {
        $result = $this->get('user.handler')->post($request);
        if ($result instanceof User) {
            $jsonResponse = json_encode(array('code' => 200, 'email' => $result->getEmail()));
            $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = $result;
        }

        return $response;
    }
    
    /**
     *
     * @View()
     *
     * @return FormTypeInterface
     */
    public function newRegisterAction()
    {
        return $this->createForm(new AppUserType());
    }

    /**
     * @param Request $request
     *
     * @View()
     * @return array
     */
    public function getRegisterAction(Request $request)
    {
        $user = $this->getOr404($request->get('email'));

        return $user;
    }

    /**
     * Fetch the Page or throw a 404 exception.
     *
     * @param mixed $id
     *
     * @return PageInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($email)
    {
        if (!($page = $this->container->get('user.handler')->get(null, $email))) {
            throw new NotFoundHttpException(sprintf('The User \'%s\' was not found.', $email));
        }

        return $page;
    }
}