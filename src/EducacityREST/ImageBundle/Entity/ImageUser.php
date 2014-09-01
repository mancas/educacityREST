<?php

namespace EducacityREST\ImageBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use EducacityREST\ImageBundle\Util\FileHelper;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * @ORM\Table()
 * @ORM\Entity()
 * @ExclusionPolicy("all")
 */
class ImageUser extends Image
{
    public $subdirectory = "images/user";
    protected $maxImages = 100;

    /**
     * @ORM\ManyToOne(targetEntity="EducacityREST\UserBundle\Entity\User", inversedBy="images")
     */
    protected $user;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Date()
     * @Expose
     */
    protected $synchronized;

    public function createCopies()
    {
        list($oldRoute, $copies) = parent::createCopies();
        if ($nav = $this->createNav()) {
            $copies[] = $nav;
        }

        return array($oldRoute, $copies);
    }

    public function getSynchronized()
    {
        return $this->synchronized;
    }

    public function setSynchronized($synchronized)
    {
        $this->synchronized = $synchronized;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

}