<?php

namespace Pastel\PlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use \DateTime;

/**
 * Comment
 *
 * @ORM\Table(name="pastel_comment")
 * @ORM\Entity(repositoryClass="Pastel\PlatformBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datePost", type="datetime")
     */
    private $datePost;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="warning", type="boolean")
     */
    private $warning;

    /**
     * @ORM\ManyToOne(targetEntity="Pastel\PlatformBundle\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Pastel\PlatformBundle\Entity\Article", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;
    
    
    public function __construct()
    {
        $this->datePost = new Datetime();
        $this->warning = false;
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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set datePost
     *
     * @param \DateTime $datePost
     *
     * @return Comment
     */
    public function setDatePost($datePost)
    {
        $this->datePost = $datePost;

        return $this;
    }

    /**
     * Get datePost
     *
     * @return \DateTime
     */
    public function getDatePost()
    {
        return $this->datePost;
    }

    /**
     * Set user
     *
     * @param \Pastel\PlatformBundle\Entity\User $user
     *
     * @return Comment
     */
    public function setUser(\Pastel\PlatformBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Pastel\PlatformBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set article
     *
     * @param \Pastel\PlatformBundle\Entity\Article $article
     *
     * @return Comment
     */
    public function setArticle(\Pastel\PlatformBundle\Entity\Article $article)
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
    
    /**
     * Set warning
     *
     * @param boolean $warning
     *
     * @return Comment
     */
    public function setWarning($warning)
    {
        $this->warning = $warning;

        return $this;
    }

    /**
     * Get warning
     *
     * @return boolean
     */
    public function getWarning()
    {
        return $this->warning;
    }    
}
