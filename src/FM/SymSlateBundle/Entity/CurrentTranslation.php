<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * CurrentTranslation
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="classification_id_language_id_idx", columns={"classification_id","language_id"})})
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\CurrentTranslationRepository")
 */
class CurrentTranslation
{
	/**
	 * @ORM\ManyToOne(targetEntity="Classification", inversedBy="current_translations")
	 * @ORM\JoinColumn(name="classification_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 */
	private $classification;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Translation", inversedBy="current_translations")
	 * @ORM\JoinColumn(name="translation_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
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
     * @ORM\Column(name="classification_id", type="integer")
     */
    private $classification_id;


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
     * Set classification_id
     *
     * @param integer $classificationId
     * @return CurrentTranslation
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
     * Set classification
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classification
     * @return CurrentTranslation
     */
    public function setClassification(\FM\SymSlateBundle\Entity\Classification $classification)
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
}