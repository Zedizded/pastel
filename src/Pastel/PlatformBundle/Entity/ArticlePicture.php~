<?php

namespace Pastel\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Picture
 *
 * @ORM\Table(name="pastel_articlePicture")
 * @ORM\Entity(repositoryClass="Pastel\PlatformBundle\Repository\ArticlePictureRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ArticlePicture
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var integer
     *
     * @ORM\Column(name="random", type="integer", nullable=true)
     */
    private $random;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    private $file;
    
    /**
     * @ORM\OneToOne(targetEntity="Pastel\PlatformBundle\Entity\Article", inversedBy="articlePicture")
     */
    private $article;
    

    public function getUploadRootDir()
    {
        return __dir__.'/../../../../web/uploads/articlesPictures';
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getAssetPath()
    {
        return 'uploads/articlesPictures/'.$this->path;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
        $this->oldFile = $this->getPath();

        if (null !== $this->file)
        {
            $this->alt = $this->file->getClientOriginalName();
            $this->path = sha1(uniqid(mt_rand(),true)).'.'.$this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null !== $this->file) {
            $this->file->move($this->getUploadRootDir(),$this->path);
            unset($this->file);

            if ($this->oldFile != null) unlink($this->tempFile);
        }
    }

    /**
     * @ORM\PreRemove()
     */
    public function preRemoveUpload()
    {
        $this->tempFile = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (file_exists($this->tempFile)) unlink($this->tempFile);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set alt
     *
     * @param string $alt
     *
     * @return Picture
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Picture
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set random
     *
     * @param string $random
     *
     * @return Picture
     */
    public function setRandom($random)
    {
        $this->random = $random;

        return $this;
    }

    /**
     * Get random
     *
     * @return string
     */
    public function getRandom()
    {
        return $this->random;
    }
    
    /**
     * Set article
     *
     * @param \Pastel\PlatformBundle\Entity\Article $article
     *
     * @return ArticlePicture
     */
    public function setArticle(\Pastel\PlatformBundle\Entity\Article $article = null)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Pastel\PlatformBundle\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }    

}
