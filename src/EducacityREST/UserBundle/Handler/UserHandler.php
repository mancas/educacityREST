<?php

namespace EducacityREST\UserBundle\Handler;
use EducacityREST\UserBundle\Entity\User;
use EducacityREST\UserBundle\Handler\UserHandlerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use EducacityREST\UserBundle\Form\AppUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class UserHandler
{
    private $em;
    private $factory;
    private $encoderFactory;
    
    public function __construct(EntityManager $em, FormFactoryInterface $formFactory, EncoderFactory $encoderFactory)
    {
        $this->em = $em;
        $this->factory = $formFactory;
        $this->encoderFactory = $encoderFactory;
    }

    public function get($id = null, $email = null)
    {
        if ($email) {
            return $this->em->getRepository('UserBundle:User')->findOneBy(array('email' => $email));
        }
        return $this->em->getRepository('UserBundle:User')->find($id);
    }

    /**
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 20, $offset = 0, $orderby = null)
    {
        return $this->em->getRepository('UserBundle:User')->findBy(array(), $orderby, $limit, $offset);
    }

    /**
     * Create a new User.
     *
     * @param $request
     *
     * @return User
     */
    public function post(Request $request)
    {
        $user = new User();

        return $this->processForm($user, $request, 'POST');
    }
    
    /**
     * @param AppUser $user
     * @param $request
     *
     * @return AppUser
     */
    public function put(User $entity, $request)
    {
        return $this->processForm($entity, $request);
    }
    
    /**
     * @param AppUser $user
     * @param $request
     *
     * @return AppUser
     */
    public function patch(User $entity, $request)
    {
        return $this->processForm($entity, $request, 'PATCH');
    }
    
    /**
     * @param AppUser $user
     *
     * @return AppUser
     */
    public function delete(User $entity)
    {
        $this->em->remove($entity);
        $this->em->flush($entity);
    }
    
    /**
     * Processes the form.
     *
     * @param User     $user
     * @param array         $parameters
     * @param String        $method
     *
     * @return AppUser
     *
     * @throws \Exception
     */
    private function processForm(User $entity, Request $request, $method = "PUT")
    {
        $form = $this->factory->create(new AppUserType(), $entity, array('method' => $method));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $req = $request->request->get('app_user');
            if (!$req) {
                $req = $request->request->get('user');
            }
            if ($req['password']!= "") {
                $entity->setPassword($req['password']);
                $encoder = $this->encoderFactory->getEncoder($entity);
                $passwordEncoded = $encoder->encodePassword($entity->getPassword(), $entity->getSalt());
                $entity->setPassword($passwordEncoded);
            }
            $this->em->persist($entity);
            $this->em->flush($entity);

            return $entity;
        }

        throw new \Exception('Invalid submitted data');
    }
}