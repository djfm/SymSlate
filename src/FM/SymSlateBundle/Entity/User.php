<?php

namespace FM\SymSlateBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
	/**
	 * @ORM\OneToMany(targetEntity="Translation", mappedBy="author", fetch="EXTRA_LAZY")
	 */
	private $authored_translations;
	
    private $translations_count = 0;
		
	/**
	 * @ORM\OneToMany(targetEntity="TranslationsImport", mappedBy="creator")
	 */
	private $translations_imports;
	
	/**
	 * @ORM\OneToMany(targetEntity="PackExport", mappedBy="creator")
	 */
	private $pack_exports;

    /**
     * @ORM\OneToMany(targetEntity="UserLanguage", mappedBy="user", cascade="persist")
     */
    private $user_languages;
	
	public function __construct()
	{
		$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->authored_translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->translations_imports = new \Doctrine\Common\Collections\ArrayCollection();
		$this->pack_exports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_languages = new \Doctrine\Common\Collections\ArrayCollection();
		
		parent::__construct();
	}
	
	public function canCreateLanguages()
	{
		return true;
	}
	
    public function isSuperAdmin()
    {
        return in_array("ROLE_SUPER_ADMIN", $this->getRoles());
    }

	public function canTranslateInto(Language $language)
	{
        if(in_array("ROLE_SUPER_ADMIN", $this->getRoles()))return true;
        else
        {
            foreach($this->getUserLanguages() as $ul)
            {
                if($ul->getLanguage()->getId() == $language->getId())return true;
            }
        }
        
		return false;
	}

    public function getTranslationsCount()
    {
        return $this->translations_count;
    }

    public function setTranslationsCount($count)
    {
        $this->translations_count = $count;
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

    /**
     * Add user_languages
     *
     * @param \FM\SymSlateBundle\Entity\UserLanguage $userLanguages
     * @return User
     */
    public function addUserLanguage(\FM\SymSlateBundle\Entity\UserLanguage $userLanguages)
    {
        $this->user_languages[] = $userLanguages;
    
        return $this;
    }

    /**
     * Remove user_languages
     *
     * @param \FM\SymSlateBundle\Entity\UserLanguage $userLanguages
     */
    public function removeUserLanguage(\FM\SymSlateBundle\Entity\UserLanguage $userLanguages)
    {
        $this->user_languages->removeElement($userLanguages);
    }

    /**
     * Get user_languages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserLanguages()
    {
        return $this->user_languages;
    }
}