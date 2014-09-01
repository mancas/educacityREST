<?php
namespace EducacityREST\RegisterBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use EducacityREST\OAuthBundle\Command\CreateOAuthClientCommand;
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
//access
//Yzk0MjE1YmVjZWU1YzIyYTAzZTY1ZWEwNzkxNjIwMGQ1NjRlNDgzOTI2NTU3M2RmY2QyNGRkMzBkNzVjMWI4NQ
//refresh NjFjOTFkYzJjYmQ2NmZjNWIwZmNlNDNmOGExMDRjOGI3Y2I3NjZkMDg4ZDIxOTBkZWVmNjdlNWU1ZWUzYTdiNA
    public function postRegisterAction(Request $request)
    {
        $result = $this->get('user.handler')->post($request);
        if ($result instanceof User) {
            $em = $this->getDoctrine()->getManager();
            $clients = $em->getRepository('OAuthBundle:Client')->findAll();
            $client = $clients[0];
            $jsonResponse = json_encode(array('code' => 200, 'client_id' => $client->getPublicId(),
                                              'client_secret' => $client->getSecret()));
            $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
            $response->headers->set('Content-Type', 'application/json');
        } else {
            $response = $result;
        }

        return $response;
    }

    public function testAction()
    {
        require_once 'Text/Wiki/Mediawiki.php';
        $wiki = new \Text_Wiki_Mediawiki();
        //$wiki = $wiki->factory('Mediawiki');
        $wiki->setRenderConf('xhtml', 'Wikilink', 'view_url',
            'http://wikipedia.org/wiki/');
        $wiki->setRenderConf('xhtml', 'Wikilink', 'pages', false);
        $wiki->setRenderConf('xhtml', 'Url', 'target', false);
        ldd($wiki->transform("{{other uses|La Giralda (disambiguation)}} {{Infobox World Heritage Site | WHS = [[Seville Cathedral|Cathedral]], Alcázar and [[Archivo General de Indias|General Archive of the Indies]] in Seville | Image = [[File:Sevilla La Giralda 18-03-2011 18-24-31.jpg|200px]]<br><small>La Giralda</small> | State Party = [[Spain]] | Type = Cultural | Criteria = i, ii, iii, vi | ID = 383 | Region = [[List of World Heritage Sites in Europe|Europe and North America]] | Year = 1987 | Session = 11th | Link = http://whc.unesco.org/en/list/383 }} The \'\'\'Giralda\'\'\' ({{lang-es|La Giralda}} ; {{lang-ar|الخيرالدة}}) is a former [[minaret]] that was converted to a [[bell tower]]", 'Xhtml'));
        return $this->render("RegisterBundle:Default:index.html.twig");
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