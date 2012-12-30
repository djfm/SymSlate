<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserLanguage
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class UserLanguage
{
	 /**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="user_languages")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $user;
	 
	 /**
	 * @ORM\ManyToOne(targetEntity="Language", inversedBy="user_languages")
	 * @ORM\JoinColumn(name="language_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	 private $language;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $user_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="language_id", type="integer")
     */
    private $language_id;

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
     * @return UserLanguage
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
     * Set language_id
     *
     * @param integer $languageId
     * @return UserLanguage
     */
    public function setLanguageId($languageId)
    {
        $this->language_id = $languageId;
    
        return $this;
    }

    /**
     * Get language_id
     *
     * @return integer 
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * Set user
     *
     * @param \FM\SymSlateBundle\Entity\User $user
     * @return UserLanguage
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
     * Set language
     *
     * @param \FM\SymSlateBundle\Entity\Language $language
     * @return UserLanguage
     */
    public function setLanguage(\FM\SymSlateBundle\Entity\Language $language = null)
    {
        $this->language = $language;
    
        return $this;
    }

    /**
     * Get language
     *
     * @return \FM\SymSlateBundle\Entity\Language 
     */
    public function getLanguage()
    {
        return $this->language;
    }
}