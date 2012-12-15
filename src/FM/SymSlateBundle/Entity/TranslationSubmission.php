<?php

namespace FM\SymSlateBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * TranslationSubmission
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\TranslationSubmissionRepository")
 */
class TranslationSubmission
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
    /**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="translation_submissions")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $user;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Classification", inversedBy="translation_submissions")
	 * @ORM\JoinColumn(name="classification_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $classification;
	

    /**
     * @var integer
     *
     * @ORM\Column(name="classification_id", type="integer")
     */
    private $classification_id;
	
	/**
	 * @ORM\OneToOne(targetEntity="Translation", inversedBy="translation_submission")
	 * @ORM\JoinColumn(name="translation_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $translation;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="translation_id", type="integer")
     */
    private $translation_id;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * Set user_id
     *
     * @param integer $userId
     * @return TranslationSubmission
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    
        return $this;
    }

    /**
     * Get user_id
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set classification_id
     *
     * @param integer $classificationId
     * @return TranslationSubmission
     */
    public function setClassificationId($classificationId)
    {
        $this->classification_id = $classificationId;
    
        return $this;
    }

    /**
     * Get classification_id
     *
     * @return integer 
     */
    public function getClassificationId()
    {
        return $this->classification_id;
    }

    /**
     * Set translation_id
     *
     * @param integer $translationId
     * @return TranslationSubmission
     */
    public function setTranslationId($translationId)
    {
        $this->translation_id = $translationId;
    
        return $this;
    }

    /**
     * Get translation_id
     *
     * @return integer 
     */
    public function getTranslationId()
    {
        return $this->translation_id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return TranslationSubmission
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set user
     *
     * @param \FM\SymSlateBundle\Entity\User $user
     * @return TranslationSubmission
     */
    public function setUser(\FM\SymSlateBundle\Entity\User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \FM\SymSlateBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set classification
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classification
     * @return TranslationSubmission
     */
    public function setClassification(\FM\SymSlateBundle\Entity\Classification $classification = null)
    {
        $this->classification = $classification;
    
        return $this;
    }

    /**
     * Get classification
     *
     * @return \FM\SymSlateBundle\Entity\Classification 
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set translation
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translation
     * @return TranslationSubmission
     */
    public function setTranslation(\FM\SymSlateBundle\Entity\Translation $translation = null)
    {
        $this->translation = $translation;
    
        return $this;
    }

    /**
     * Get translation
     *
     * @return \FM\SymSlateBundle\Entity\Translation 
     */
    public function getTranslation()
    {
        return $this->translation;
    }
}