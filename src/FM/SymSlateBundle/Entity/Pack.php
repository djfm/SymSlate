<?php

namespace FM\SymSlateBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pack
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="project_name_version_idx", columns={"project", "name", "version"})})
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\PackRepository")
 */
class Pack
{
	
	/**
	 * @ORM\OneToMany(targetEntity="Classification", mappedBy="message")
	 */
	 private $classifications;
	 
	 /**
	 * @ORM\OneToMany(targetEntity="MessagesImport", mappedBy="pack")
	 */
	 private $messages_imports;
	 
	 /**
	 * @ORM\OneToMany(targetEntity="Storage", mappedBy="pack")
	 */
	 private $storages;
	 
	 public function __construct()
	 {
	 	$this->classifications  = new \Doctrine\Common\Collections\ArrayCollection();
		$this->messages_imports = new \Doctrine\Common\Collections\ArrayCollection();
		$this->storages         = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @ORM\Column(name="project", type="string", length=255)
     */
    private $project;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=255)
     */
    private $version;
	
	
	/**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer")
     */
	private $created_by;
	
	
	/**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

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
     * Set project
     *
     * @param string $project
     * @return Pack
     */
    public function setProject($project)
    {
        $this->project = $project;
    
        return $this;
    }

    /**
     * Get project
     *
     * @return string 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Pack
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
     * Set version
     *
     * @param string $version
     * @return Pack
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }
	
	public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set created_by
     *
     * @param integer $createdBy
     * @return Pack
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
     * Set created
     *
     * @param \DateTime $created
     * @return Pack
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Pack
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }
	

    /**
     * Add classifications
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classifications
     * @return Pack
     */
    public function addClassification(\FM\SymSlateBundle\Entity\Classification $classifications)
    {
        $this->classifications[] = $classifications;
    
        return $this;
    }

    /**
     * Remove classifications
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classifications
     */
    public function removeClassification(\FM\SymSlateBundle\Entity\Classification $classifications)
    {
        $this->classifications->removeElement($classifications);
    }

    /**
     * Get classifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClassifications()
    {
        return $this->classifications;
    }

    /**
     * Add messages_imports
     *
     * @param \FM\SymSlateBundle\Entity\MessagesImport $messagesImports
     * @return Pack
     */
    public function addMessagesImport(\FM\SymSlateBundle\Entity\MessagesImport $messagesImports)
    {
        $this->messages_imports[] = $messagesImports;
    
        return $this;
    }

    /**
     * Remove messages_imports
     *
     * @param \FM\SymSlateBundle\Entity\MessagesImport $messagesImports
     */
    public function removeMessagesImport(\FM\SymSlateBundle\Entity\MessagesImport $messagesImports)
    {
        $this->messages_imports->removeElement($messagesImports);
    }

    /**
     * Get messages_imports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessagesImports()
    {
        return $this->messages_imports;
    }

    /**
     * Add storages
     *
     * @param \FM\SymSlateBundle\Entity\Storage $storages
     * @return Pack
     */
    public function addStorage(\FM\SymSlateBundle\Entity\Storage $storages)
    {
        $this->storages[] = $storages;
    
        return $this;
    }

    /**
     * Remove storages
     *
     * @param \FM\SymSlateBundle\Entity\Storage $storages
     */
    public function removeStorage(\FM\SymSlateBundle\Entity\Storage $storages)
    {
        $this->storages->removeElement($storages);
    }

    /**
     * Get storages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStorages()
    {
        return $this->storages;
    }
}