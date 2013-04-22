<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * History
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\HistoryRepository")
 * @ORM\HasLifecycleCallbacks
 */
class History
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
    * @ORM\ManyToOne(targetEntity="Message")
    * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $message;

	/**
	* @var integer
	*
	* @ORM\Column(name="message_id", type="integer", nullable=false)
	*/
    private $message_id;

    /**
    * @ORM\ManyToOne(targetEntity="Language")
    * @ORM\JoinColumn(name="language_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $language;

    /**
	* @var integer
	*
	* @ORM\Column(name="language_id", type="integer", nullable=false)
	*/
    private $language_id;

    /**
    * @ORM\ManyToOne(targetEntity="Translation")
    * @ORM\JoinColumn(name="translation_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $translation;

    /**
	* @var integer
	*
	* @ORM\Column(name="translation_id", type="integer", nullable=false)
	*/
    private $translation_id;

    /**
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
    */
    private $user;

    /**
	* @var integer
	*
	* @ORM\Column(name="user_id", type="integer", nullable=false)
	*/
    private $user_id;

    /**
	* @var string
	*
	* @ORM\Column(name="operation", type="string", length=32)
	*/
    private $operation;

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
     * Set message_id
     *
     * @param integer $messageId
     * @return History
     */
    public function setMessageId($messageId)
    {
        $this->message_id = $messageId;
    
        return $this;
    }

    /**
     * Get message_id
     *
     * @return integer 
     */
    public function getMessageId()
    {
        return $this->message_id;
    }

    /**
     * Set language_id
     *
     * @param integer $languageId
     * @return History
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
     * Set translation_id
     *
     * @param integer $translationId
     * @return History
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
     * Set user_id
     *
     * @param integer $userId
     * @return History
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
     * Set operation
     *
     * @param string $operation
     * @return History
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    
        return $this;
    }

    /**
     * Get operation
     *
     * @return string 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return History
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
     * Set message
     *
     * @param \FM\SymSlateBundle\Entity\Message $message
     * @return History
     */
    public function setMessage(\FM\SymSlateBundle\Entity\Message $message = null)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return \FM\SymSlateBundle\Entity\Message 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set language
     *
     * @param \FM\SymSlateBundle\Entity\Language $language
     * @return History
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

    /**
     * Set translation
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translation
     * @return History
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
     * @return History
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