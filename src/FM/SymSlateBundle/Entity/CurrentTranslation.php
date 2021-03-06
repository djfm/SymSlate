<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CurrentTranslation
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="fm_message_id_language_id_idx", columns={"message_id","language_id"})})
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\CurrentTranslationRepository")
 */
class CurrentTranslation
{	
	/**
	 * @ORM\ManyToOne(targetEntity="Message", inversedBy="current_translations")
	 * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $message;
		
	/**
	 * @ORM\ManyToOne(targetEntity="Translation", inversedBy="current_translations")
	 * @ORM\JoinColumn(name="translation_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $translation;

    /**
     * @ORM\ManyToOne(targetEntity="Language", inversedBy="current_translations")
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
     * @ORM\Column(name="translation_id", type="integer")
     */
    private $translation_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="language_id", type="integer")
     */
    private $language_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="message_id", type="integer")
     */
    private $message_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="fuzzyness", type="integer", nullable=false)
     */
    private $fuzzyness = 0;

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
     * Set translation_id
     *
     * @param integer $translationId
     * @return CurrentTranslation
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
     * Set language_id
     *
     * @param integer $languageId
     * @return CurrentTranslation
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
     * Set message_id
     *
     * @param integer $messageId
     * @return CurrentTranslation
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
     * Set message
     *
     * @param \FM\SymSlateBundle\Entity\Message $message
     * @return CurrentTranslation
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
     * Set translation
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translation
     * @return CurrentTranslation
     */
    public function setTranslation(\FM\SymSlateBundle\Entity\Translation $translation)
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
     * Set language
     *
     * @param \FM\SymSlateBundle\Entity\Language $language
     * @return CurrentTranslation
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
     * Set fuzzyness
     *
     * @param integer $fuzzyness
     * @return CurrentTranslation
     */
    public function setFuzzyness($fuzzyness)
    {
        $this->fuzzyness = $fuzzyness;
    
        return $this;
    }

    /**
     * Get fuzzyness
     *
     * @return integer 
     */
    public function getFuzzyness()
    {
        return $this->fuzzyness;
    }
}