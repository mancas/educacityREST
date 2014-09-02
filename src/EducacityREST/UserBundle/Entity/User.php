<?php
namespace EducacityREST\UserBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use EducacityREST\ImageBundle\Entity\ImageUser;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="EducacityREST\UserBundle\Entity\UserRepository")
 * @UniqueEntity("email")
 * @ExclusionPolicy("all")
 */
class User implements UserInterface, \Serializable, EquatableInterface
{
    const AUTH_SALT = "Hyk3T1K0FWjo";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @Expose
     * */
    protected $id;


    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     * @Expose
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=250, unique=true)
     * @Assert\Email()
     * @Expose
     * */
    protected $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(min = 6)
     * */
    protected $password;

    /**
     * @var string salt
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     * @Expose
     */
    protected $salt;

    /**
     * @ORM\OneToMany(targetEntity="EducacityREST\ImageBundle\Entity\ImageUser", mappedBy="user")
     */
    protected $images;

    /**
     * @ORM\Column(name="public", type="boolean", nullable=true, options={"default" = 0})
     */
    protected $public;

    /**
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     * @Expose
     */
    protected $updatedDate;

    /**
     * @var date $registeredDate
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="registeredDate", type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $registeredDate;

    /**
     * @ORM\Column(name="validated", type="boolean", nullable=true, options={"default" = 0})
     * @Expose
     */
    protected $validated = false;

    protected function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setRegisteredDate($registeredDate)
    {
        $this->registeredDate = $registeredDate;
    }

    public function getRegisteredDate()
    {
        return $this->registeredDate;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $updatedDate
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;
    }

    /**
     * @return mixed
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    public function serialize()
    {
        return serialize(array($this->id, $this->password, $this->email));
    }

    public function unserialize($serialized)
    {
        list($this->id, $this->password, $this->email) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    public function isEqualTo(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        return $this->getEmail() == $user->getEmail();
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    /**
     * @param mixed $validated
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;
    }

    /**
     * @return mixed
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return $this->images;
    }

    public function addImage($image)
    {
        $this->images->add($image);
    }

    /**
     * @param mixed $public
     */
    public function setPublic($public)
    {
        $this->public = $public;
    }

    /**
     * @return mixed
     */
    public function getPublic()
    {
        return $this->public;
    }
    
}
