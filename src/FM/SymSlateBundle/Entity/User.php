<?php

namespace FM\SymSlateBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User extends BaseUser
{
	/**
	 * @ORM\OneToMany(targetEntity="Translation", mappedBy="author")
	 */
	private $authored_translations;
	
	/**
	 * @ORM\OneToMany(targetEntity="Translation", mappedBy="reviewer")
	 */
	private $reviewed_translations;
	
	public function __construct()
	{
		$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->authored_translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->reviewed_translations = new \Doctrine\Common\Collections\ArrayCollection();
		
		parent::__construct();
	}
	
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add authored_translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $authoredTranslations
     * @return User
     */
    public function addAuthoredTranslation(\FM\SymSlateBundle\Entity\Translation $authoredTranslations)
    {
        $this->authored_translations[] = $authoredTranslations;
    
        return $this;
    }

    /**
     * Remove authored_translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $authoredTranslations
     */
    public function removeAuthoredTranslation(\FM\SymSlateBundle\Entity\Translation $authoredTranslations)
    {
        $this->authored_translations->removeElement($authoredTranslations);
    }

    /**
     * Get authored_translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAuthoredTranslations()
    {
        return $this->authored_translations;
    }

    /**
     * Add reviewed_translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $reviewedTranslations
     * @return User
     */
    public function addReviewedTranslation(\FM\SymSlateBundle\Entity\Translation $reviewedTranslations)
    {
        $this->reviewed_translations[] = $reviewedTranslations;
    
        return $this;
    }

    /**
     * Remove reviewed_translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $reviewedTranslations
     */
    public function removeReviewedTranslation(\FM\SymSlateBundle\Entity\Translation $reviewedTranslations)
    {
        $this->reviewed_translations->removeElement($reviewedTranslations);
    }

    /**
     * Get reviewed_translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviewedTranslations()
    {
        return $this->reviewed_translations;
    }
}