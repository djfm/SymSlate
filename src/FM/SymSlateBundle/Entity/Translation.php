<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation
 *
 * @ORM\Table()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\TranslationRepository")
 */
class Translation
{
	/**
	 * @ORM\ManyToOne(targetEntity="TranslationsImport", inversedBy="messages", cascade={"persist"})
	 * @ORM\JoinColumn(name="translations_import_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	private $translations_import;
	 
    /**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="authored_translations", cascade={"persist"})
	 * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	private $author;
	 
    /**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="authored_translations", cascade={"persist"})
	 * @ORM\JoinColumn(name="reviewed_by", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	private $reviewer;
     
    /**
     * @ORM\OneToOne(targetEntity="Classification", mappedBy="translation")
     */
    private $classification;
	
	
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
     * @ORM\Column(name="translations_import_id", type="integer")
     */
    private $translations_import_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $created_by;

    /**
     * @var integer
     *
     * @ORM\Column(name="reviewed_by", type="integer")
     */
    private $reviewed_by;

    /**
     * @var integer
     *
     * @ORM\Column(name="previous_translation_id", type="integer")
     */
    private $previous_translation_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="language_id", type="integer")
     */
    private $language_id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;


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
     * Set translations_import_id
     *
     * @param integer $translationsImportId
     * @return Translation
     */
    public function setTranslationsImportId($translationsImportId)
    {
        $this->translations_import_id = $translationsImportId;
    
        return $this;
    }

    /**
     * Get translations_import_id
     *
     * @return integer 
     */
    public function getTranslationsImportId()
    {
        return $this->translations_import_id;
    }

    /**
     * Set created_by
     *
     * @param integer $createdBy
     * @return Translation
     */
    public function setCreatedBy($createdBy)
    {
        $this->created_by = $createdBy;
    
        return $this;
    }

    /**
     * Get created_by
     *
     * @return integer 
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * Set reviewed_by
     *
     * @param integer $reviewedBy
     * @return Translation
     */
    public function setReviewedBy($reviewedBy)
    {
        $this->reviewed_by = $reviewedBy;
    
        return $this;
    }

    /**
     * Get reviewed_by
     *
     * @return integer 
     */
    public function getReviewedBy()
    {
        return $this->reviewed_by;
    }

    /**
     * Set previous_translation_id
     *
     * @param integer $previousTranslationId
     * @return Translation
     */
    public function setPreviousTranslationId($previousTranslationId)
    {
        $this->previous_translation_id = $previousTranslationId;
    
        return $this;
    }

    /**
     * Get previous_translation_id
     *
     * @return integer 
     */
    public function getPreviousTranslationId()
    {
        return $this->previous_translation_id;
    }

    /**
     * Set language_id
     *
     * @param integer $languageId
     * @return Translation
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
     * Set text
     *
     * @param string $text
     * @return Translation
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set translations_import
     *
     * @param \FM\SymSlateBundle\Entity\TranslationsImport $translationsImport
     * @return Translation
     */
    public function setTranslationsImport(\FM\SymSlateBundle\Entity\TranslationsImport $translationsImport = null)
    {
        $this->translations_import = $translationsImport;
    
        return $this;
    }

    /**
     * Get translations_import
     *
     * @return \FM\SymSlateBundle\Entity\TranslationsImport 
     */
    public function getTranslationsImport()
    {
        return $this->translations_import;
    }

    /**
     * Set author
     *
     * @param \FM\SymSlateBundle\Entity\User $author
     * @return Translation
     */
    public function setAuthor(\FM\SymSlateBundle\Entity\User $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \FM\SymSlateBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set reviewer
     *
     * @param \FM\SymSlateBundle\Entity\User $reviewer
     * @return Translation
     */
    public function setReviewer(\FM\SymSlateBundle\Entity\User $reviewer = null)
    {
        $this->reviewer = $reviewer;
    
        return $this;
    }

    /**
     * Get reviewer
     *
     * @return \FM\SymSlateBundle\Entity\User 
     */
    public function getReviewer()
    {
        return $this->reviewer;
    }

    /**
     * Set classification
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classification
     * @return Translation
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
}