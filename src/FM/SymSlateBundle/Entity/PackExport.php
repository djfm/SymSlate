<?php

namespace FM\SymSlateBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * PackExport
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\PackExportRepository")
 * @ORM\HasLifecycleCallbacks
 */
class PackExport
{
	/**
	 * @var creator
	 * 
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="pack_exports")
	 * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="CASCADE")
	 * 
	 */
	private $creator;
	
	/**
	 * @var pack
	 * 
	 * @ORM\ManyToOne(targetEntity="Pack", inversedBy="pack_exports")
	 * @ORM\JoinColumn(name="pack_id", referencedColumnName="id", onDelete="CASCADE")
	 * 
	 */
	private $pack;
	
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
     * @ORM\Column(name="pack_id", type="integer")
     */
    private $pack_id;

    /**
     * @ORM\ManyToOne(targetEntity="Language")
     */
    private $language;

    /**
     * @var integer
     *
     * @ORM\Column(name="language_id", type="integer")
     */
    private $language_id;

    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="string", length=255, nullable=true)
     */
    private $filepath;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer")
     */
    private $created_by;


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
     * Set pack_id
     *
     * @param integer $packId
     * @return PackExport
     */
    public function setPackId($packId)
    {
        $this->pack_id = $packId;
    
        return $this;
    }

    /**
     * Get pack_id
     *
     * @return integer 
     */
    public function getPackId()
    {
        return $this->pack_id;
    }

    /**
     * Set language_id
     *
     * @param integer $languageId
     * @return PackExport
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
     * Set filepath
     *
     * @param string $filepath
     * @return PackExport
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
    
        return $this;
    }

    /**
     * Get filepath
     *
     * @return string 
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return PackExport
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
     * Set created_by
     *
     * @param integer $createdBy
     * @return PackExport
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
     * Set creator
     *
     * @param \FM\SymSlateBundle\Entity\User $creator
     * @return PackExport
     */
    public function setCreator(\FM\SymSlateBundle\Entity\User $creator = null)
    {
        $this->creator = $creator;
    
        return $this;
    }

    /**
     * Get creator
     *
     * @return \FM\SymSlateBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }
	
	public function getAbsolutePath()
    {
        return null === $this->filepath
            ? null
            : $this->getUploadRootDir().'/'.$this->filepath;
    }

    public function getWebPath()
    {
        return null === $this->filepath
            ? null
            : $this->getUploadDir().'/'.$this->filepath;
    }
	
	public function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        $dir = __DIR__.'/../../../../web/'.$this->getUploadDir();
		if (!is_dir($dir))
		{
		    mkdir($dir);
		}
		return $dir;
    }
	
	protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'exports';
    }
	
	/**
     * @ORM\PostRemove()
     */
    public function removeFile()
    {
        if ($file = $this->getAbsolutePath() and file_exists($file)) {
            unlink($file);
        }
    }
	

    /**
     * Set pack
     *
     * @param \FM\SymSlateBundle\Entity\Pack $pack
     * @return PackExport
     */
    public function setPack(\FM\SymSlateBundle\Entity\Pack $pack = null)
    {
        $this->pack = $pack;
    
        return $this;
    }

    /**
     * Get pack
     *
     * @return \FM\SymSlateBundle\Entity\Pack 
     */
    public function getPack()
    {
        return $this->pack;
    }
	

    /**
     * Set language
     *
     * @param \FM\SymSlateBundle\Entity\Language $language
     * @return PackExport
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