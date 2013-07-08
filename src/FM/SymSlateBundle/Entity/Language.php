<?php

namespace FM\SymSlateBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Language
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\LanguageRepository")
 */
class Language
{
	/**
	 * @ORM\OneToMany(targetEntity="Translation", mappedBy="language")
	 */
	private $translations;

    /**
     * @ORM\OneToMany(targetEntity="CurrentTranslation", mappedBy="language")
     */
    private $current_translations;
	
	/**
	 * @ORM\OneToMany(targetEntity="CountryLanguage", mappedBy="language")
	 */
	private $country_languages;

    /**
     * @ORM\OneToMany(targetEntity="UserLanguage", mappedBy="language")
     */
    private $user_languages;
	
	public function __construct()
	{
		$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
		$this->country_languages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user_languages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->current_translations = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	public function __toString()
	{
		return $this->getAName();
	}
	
	public function getAName()
	{
		if($n = $this->getName())return $n;
		else return $this->getCode();
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
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=255, nullable=true)
     */
    private $locale;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = false;


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
     * Set code
     *
     * @param string $code
     * @return Language
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set locale
     *
     * @param string $locale
     * @return Language
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    
        return $this;
    }

    /**
     * Get locale
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Language
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translations
     * @return Language
     */
    public function addTranslation(\FM\SymSlateBundle\Entity\Translation $translations)
    {
        $this->translations[] = $translations;
    
        return $this;
    }

    /**
     * Remove translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translations
     */
    public function removeTranslation(\FM\SymSlateBundle\Entity\Translation $translations)
    {
        $this->translations->removeElement($translations);
    }

    /**
     * Get translations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Add country_languages
     *
     * @param \FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages
     * @return Language
     */
    public function addCountryLanguage(\FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages)
    {
        $this->country_languages[] = $countryLanguages;
    
        return $this;
    }

    /**
     * Remove country_languages
     *
     * @param \FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages
     */
    public function removeCountryLanguage(\FM\SymSlateBundle\Entity\CountryLanguage $countryLanguages)
    {
        $this->country_languages->removeElement($countryLanguages);
    }

    /**
     * Get country_languages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCountryLanguages()
    {
        return $this->country_languages;
    }

    /**
     * Add user_languages
     *
     * @param \FM\SymSlateBundle\Entity\UserLanguage $userLanguages
     * @return Language
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

    /**
     * Add current_translations
     *
     * @param \FM\SymSlateBundle\Entity\CurrentTranslation $currentTranslations
     * @return Language
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
     * Set published
     *
     * @param boolean $published
     * @return Language
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }
}
