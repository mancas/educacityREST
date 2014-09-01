<?php

namespace EducacityREST\OAuthBundle\Controller;

use EducacityREST\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class AccessController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = '';
        }

        if ($error) {
            $error = $error->getMessage(
            ); // WARNING! Symfony source code identifies this line as a potential security threat.
        }

        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        return $this->render(
            'OAuthBundle:Access:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'error' => $error,
            )
        );
    }

    public function loginCheckAction(Request $request)
    {

    }

    public function postLoginAction(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = $this->get('user.handler')->get(null, $username);;

        if(!($user instanceof User)){
            $jsonResponse = json_encode(array('code' => 404, 'error' => 'User not exists'));
            $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        if(!$this->checkUserPassword($user, $password)){
            $jsonResponse = json_encode(array('code' => 404, 'error' => 'Wrong password'));
            $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }

        $em = $this->getDoctrine()->getManager();
        $clients = $em->getRepository('OAuthBundle:Client')->findAll();
        $client = $clients[0];
        $jsonResponse = json_encode(array('code' => 200, 'client_id' => $client->getPublicId(),
            'client_secret' => $client->getSecret));

        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    protected function checkUserPassword(User $user, $password)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);
        if(!$encoder){
            return false;
        }
        $passwordEncoded = $encoder->encodePassword($password, $user->getSalt());
        return $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());
    }

    public function setUserAction(Request $request)
    {
        $accessToken = $request->get('access_token');
        $refreshToken = $request->get('refresh_token');
        $email = $request->get('email');

        $em = $this->getDoctrine()->getManager();
        $accessTokenEntity = $em->getRepository('OAuthBundle:AccessToken')->findOneByToken($accessToken);
        $refreshTokenEntity = $em->getRepository('OAuthBundle:RefreshToken')->findOneByToken($refreshToken);
        $user = $em->getRepository('UserBundle:User')->findOneByEmail($email);

        $accessTokenEntity->setUser($user);
        $refreshTokenEntity->setUser($user);

        $em->persist($accessTokenEntity);
        $em->persist($refreshTokenEntity);
        $em->flush();

        $jsonResponse = json_encode(array('code' => 200));

        $response = new \Symfony\Component\HttpFoundation\Response($jsonResponse);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}