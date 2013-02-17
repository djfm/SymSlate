<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Translation
 *
 * @ORM\Table(indexes={@ORM\Index(name="mkey_language_id_text_idx", columns={"mkey","language_id", "text"})})
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\TranslationRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Translation
{
	/**
	 * @ORM\ManyToOne(targetEntity="TranslationsImport", inversedBy="messages")
	 * @ORM\JoinColumn(name="translations_import_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	private $translations_import;
	 
    /**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="authored_translations")
	 * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	private $author;
		 
    /**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="authored_translations")
	 * @ORM\JoinColumn(name="reviewed_by", referencedColumnName="id", onDelete="CASCADE", nullable=true)
	 */
	private $reviewer;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Language", inversedBy="translations")
	 * @ORM\JoinColumn(name="language_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
	 */
	private $language;
	
	/**
	 * @ORM\OneToMany(targetEntity="CurrentTranslation", mappedBy="translation")
	 */
	private $current_translations;

    /**
     * @ORM\OneToOne(targetEntity="TranslationSubmission", inversedBy="translation")
     * @ORM\JoinColumn(name="translation_submission_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $translation_submission;
	 
	public function __construct()
	{
		$this->messages = new \Doctrine\Common\Collections\ArrayCollection();
		$this->storages = new \Doctrine\Common\Collections\ArrayCollection();
		$this->current_translations = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	
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
     * @ORM\Column(name="translations_import_id", type="integer", nullable=true)
     */
    private $translations_import_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="translation_submission_id", type="integer", nullable=true)
     */
    private $translation_submission_id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="mass_imported", type="boolean", nullable=false)
     */
    private $mass_imported = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $created_by;

    /**
     * @var integer
     *
     * @ORM\Column(name="reviewed_by", type="integer", nullable=true)
     */
    private $reviewed_by;

    /**
     * @var integer
     *
     * @ORM\Column(name="previous_translation_id", type="integer", nullable=true)
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
     * @ORM\Column(name="mkey", type="string", length=255)
     */
    private $mkey;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;


    /**
     * @var bool
     *
     * @ORM\Column(name="has_error", type="boolean")
     */
    private $has_error = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="has_warning", type="boolean")
     */
    private $has_warning = false;


    /**
     * @var string
     *
     * @ORM\Column(name="error_message", type="text")
     */
    private $error_message = '';

    /**
     * @var string
     *
     * @ORM\Column(name="warning_message", type="text")
     */
    private $warning_message = '';


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
        //Remove the backslashes, we don't need no escape we have Doctrine
        $this->text = preg_replace("/\\\\+('|\")/",'$1',$text);
    
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
     * Set mkey
     *
     * @param string $mkey
     * @return Translation
     */
    public function setMkey($mkey)
    {
        $this->mkey = $mkey;
    
        return $this;
    }

    /**
     * Get mkey
     *
     * @return string 
     */
    public function getMkey()
    {
        return $this->mkey;
    }

    /**
     * Set language
     *
     * @param \FM\SymSlateBundle\Entity\Language $language
     * @return Translation
     */
    public function setLanguage(\FM\SymSlateBundle\Entity\Language $language)
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
     * Add current_translations
     *
     * @param \FM\SymSlateBundle\Entity\CurrentTranslation $currentTranslations
     * @return Translation
     */
    public function addCurrentTranslation(\FM\SymSlateBundle\Entity\CurrentTranslation $currentTranslations)
    {
        $this->current_translations[] = $currentTranslations;
    
        return $this;
    }

    /**
     * Remove current_translations
     *
     * @param \FM\SymSlateBundle\Entity\CurrentTranslation $currentTranslations
     */
    public function removeCurrentTranslation(\FM\SymSlateBundle\Entity\CurrentTranslation $currentTranslations)
    {
        $this->current_translations->removeElement($currentTranslations);
    }

    /**
     * Get current_translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCurrentTranslations()
    {
        return $this->current_translations;
    }

    /**
     * Set translation_submission
     *
     * @param \FM\SymSlateBundle\Entity\TranslationSubmission $translationSubmission
     * @return Translation
     */
    public function setTranslationSubmission(\FM\SymSlateBundle\Entity\TranslationSubmission $translationSubmission = null)
    {
        $this->translation_submission = $translationSubmission;
    
        return $this;
    }

    /**
     * Get translation_submission
     *
     * @return \FM\SymSlateBundle\Entity\TranslationSubmission 
     */
    public function getTranslationSubmission()
    {
        return $this->translation_submission;
    }

    /**
     * Set message
     *
     * @param \FM\SymSlateBundle\Entity\Message $message
     * @return Translation
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
     * Set translation_submission_id
     *
     * @param integer $translationSubmissionId
     * @return Translation
     */
    public function setTranslationSubmissionId($translationSubmissionId)
    {
        $this->translation_submission_id = $translationSubmissionId;
    
        return $this;
    }

    /**
     * Get translation_submission_id
     *
     * @return integer 
     */
    public function getTranslationSubmissionId()
    {
        return $this->translation_submission_id;
    }

    /**
     * Set mass_imported
     *
     * @param boolean $massImported
     * @return Translation
     */
    public function setMassImported($massImported)
    {
        $this->mass_imported = $massImported;
    
        return $this;
    }

    /**
     * Get mass_imported
     *
     * @return boolean 
     */
    public function getMassImported()
    {
        return $this->mass_imported;
    }

    /**
     * Set has_error
     *
     * @param boolean $hasError
     * @return Translation
     */
    public function setHasError($hasError)
    {
        $this->has_error = $hasError;
    
        return $this;
    }

    /**
     * Get has_error
     *
     * @return boolean 
     */
    public function getHasError()
    {
        return $this->has_error;
    }

    /**
     * Set has_warning
     *
     * @param boolean $hasWarning
     * @return Translation
     */
    public function setHasWarning($hasWarning)
    {
        $this->has_warning = $hasWarning;
    
        return $this;
    }

    /**
     * Get has_warning
     *
     * @return boolean 
     */
    public function getHasWarning()
    {
        return $this->has_warning;
    }

    /**
     * Set error_message
     *
     * @param string $errorMessage
     * @return Translation
     */
    public function setErrorMessage($errorMessage)
    {
        $this->error_message = $errorMessage;
    
        return $this;
    }

    /**
     * Get error_message
     *
     * @return string 
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Set warning_message
     *
     * @param string $warningMessage
     * @return Translation
     */
    public function setWarningMessage($warningMessage)
    {
        $this->warning_message = $warningMessage;
    
        return $this;
    }

    /**
     * Get warning_message
     *
     * @return string 
     */
    public function getWarningMessage()
    {
        return $this->warning_message;
    }
}