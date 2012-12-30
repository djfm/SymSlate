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
     * @ORM\OneToOne(targetEntity="Translation", mappedBy="translation_submission")
     */
     private $translation;
	 
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
}