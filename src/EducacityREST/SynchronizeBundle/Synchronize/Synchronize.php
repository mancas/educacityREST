<?php
namespace EducacityREST\SynchronizeBundle\Synchronize;

use Doctrine\ORM\EntityManager;
use Symfony\Component\BrowserKit\Request;
use EducacityREST\UserBundle\Entity\User;
use IHorseREST\VeterinaryBundle\Entity\Veterinary;
use IHorseREST\SynchronizeBundle\Util\ArrayHelper;
use IHorseREST\ImageBundle\Entity\ImageHorse;
use IHorseREST\ImageBundle\Util\ImageHelper;

class Synchronize
{
    private $em;
    private $sync;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->sync = array();
    }

    /*public function synchronize($mobile)
    {
        if (isset($mobile['veterinary'])) {
            $this->lastSync = $mobileDB['veterinary']['last_sync_images'];
        }

        if (isset($mobileDB['images'])) {
            $images=$this->compareImages($mobileDB['images'], $veterinary);
        } else {
            $images=$this->compareImages(array(), $veterinary);
        }

        return array('images'=>$this->processImageArray($images));
    }

    private function compareImages($images, Veterinary $veterinary)
    {
        $entities = array();
        foreach ($images as $imageMobile) {
            $imageDB=$this->em->getRepository('ImageBundle:ImageHorse')->findOneBySalt($imageMobile['salt']);
            if (!$imageDB) {
                $imageDB = new ImageHorse();
                $imageDB->setSalt($imageMobile['salt']);
                $this->em->persist($imageDB);
                $this->em->flush();
                $this->saveImage($imageMobile, $imageDB);
                $entities[] = $imageDB->getId();
            } else {
                $entities[] = $imageDB->getId();
            }
        }

        return $this->em->getRepository('ImageBundle:ImageHorse')->findNotInEntities($entities, $veterinary->getClinic()->getId(), $this->lastSync);
    }

    private function saveImage($imageMobile,ImageHorse $imageDB)
    {
        foreach ($imageMobile as $property => $value) {
            if ($property=='dental_salt') {
                $dental=$this->em->getRepository('ToothBundle:Dental')->findOneBySalt($value);
                $dental->addImage($imageDB);
                $this->em->persist($dental);
                $imageDB->setDental($dental);
            } elseif ($property=='image') {
                if (ImageHelper::fromOctetToJpg($value, $imageDB)) {
                    $imageDB->setImage($imageDB->getId().'.jpg');
                    list($oldRoute, $copies) = $imageDB->createCopies();
                    $imageDB->uploadNewCopies($copies);
                    foreach ($copies as $copy){
                        $this->em->persist($copy);
                        $this->em->flush($copy);
                    }
                } else {
                    $this->em->remove($imageDB);
                    $this->em->flush();
                }
            } elseif ($property != 'modified' && $property != 'created' && $property != 'deleted') {
                if ($value) {
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($imageDB, $method)) {
                        $imageDB->$method($value);
                    }
                }
            } else {
                if ($value) {
                    $date=new \DateTime($value);
                    $method = sprintf('set%s', ucwords($property));
                    if (method_exists($imageDB, $method)) {
                        $imageDB->$method($date);
                    }
                }
            }
        }

        $date=new \DateTime('now');
        $imageDB->setSynchronized($date);

        $this->em->persist($imageDB);
        $this->em->flush();
    }

    private function processImageArray($images)
    {
        $images=ArrayHelper::flattMultilevelEntityArray($images);
        foreach ($images as & $image) {
            $image['image']=ImageHelper::fromImageToBase($image);
        }

        return $images;
    }
*/
}