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
     * @ORM\Column(type="array", name="roles")
     */
     protected $roles;
	/**
	 * @ORM\OneToMany(targetEntity="Translation", mappedBy="author")
	 */
	private $authored_translations;
	
	/**
	 * @ORM\OneToMany(targetEntity="Translation", mappedBy="reviewer")
	 */
	private $reviewed_translations;
	
	/**
	 * @ORM\OneToMany(targetEntity="TranslationSubmission", mappedBy="user")
	 */
	private $translation_submissions;
	
	/**
	 * @ORM\OneToMany(targetEntity="TranslationsImport", mappedBy="creator")
	 */
	private $translations_imports;
	
	/**
	 * @ORM\OneToMany(targetEntity="PackExport", mappedBy="creator")
	 */
	private $pack_exports;
	
	public function __construct()
	{
		$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->authored_translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->reviewed_translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->translations_imports = new \Doctrine\Common\Collections\ArrayCollection();
		$this->translation_submissions = new \Doctrine\Common\Collections\ArrayCollection();
		$this->pack_exports = new \Doctrine\Common\Collections\ArrayCollection();
		
		parent::__construct();
	}
	
	public function canCreateLanguages()
	{
		return true;
	}
	
	public function canTranslateInto(Language $language)
	{
		return true;
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

    /**
     * Add translations_imports
     *
     * @param \FM\SymSlateBundle\Entity\TranslationsImport $translationsImports
     * @return User
     */
    public function addTranslationsImport(\FM\SymSlateBundle\Entity\TranslationsImport $translationsImports)
    {
        $this->translations_imports[] = $translationsImports;
    
        return $this;
    }

    /**
     * Remove translations_imports
     *
     * @param \FM\SymSlateBundle\Entity\TranslationsImport $translationsImports
     */
    public function removeTranslationsImport(\FM\SymSlateBundle\Entity\TranslationsImport $translationsImports)
    {
        $this->translations_imports->removeElement($translationsImports);
    }

    /**
     * Get translations_imports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslationsImports()
    {
        return $this->translations_imports;
    }

    /**
     * Add translation_submissions
     *
     * @param \FM\SymSlateBundle\Entity\TranslationSubmission $translationSubmissions
     * @return User
     */
    public function addTranslationSubmission(\FM\SymSlateBundle\Entity\TranslationSubmission $translationSubmissions)
    {
        $this->translation_submissions[] = $translationSubmissions;
    
        return $this;
    }

    /**
     * Remove translation_submissions
     *
     * @param \FM\SymSlateBundle\Entity\TranslationSubmission $translationSubmissions
     */
    public function removeTranslationSubmission(\FM\SymSlateBundle\Entity\TranslationSubmission $translationSubmissions)
    {
        $this->translation_submissions->removeElement($translationSubmissions);
    }

    /**
     * Get translation_submissions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslationSubmissions()
    {
        return $this->translation_submissions;
    }

    /**
     * Add pack_exports
     *
     * @param \FM\SymSlateBundle\Entity\PackExport $packExports
     * @return User
     */
    public function addPackExport(\FM\SymSlateBundle\Entity\PackExport $packExports)
    {
        $this->pack_exports[] = $packExports;
    
        return $this;
    }

    /**
     * Remove pack_exports
     *
     * @param \FM\SymSlateBundle\Entity\PackExport $packExports
     */
    public function removePackExport(\FM\SymSlateBundle\Entity\PackExport $packExports)
    {
        $this->pack_exports->removeElement($packExports);
    }

    /**
     * Get pack_exports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPackExports()
    {
        return $this->pack_exports;
    }
}