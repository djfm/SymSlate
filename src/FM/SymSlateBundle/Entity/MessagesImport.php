<?php

namespace FM\SymSlateBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MessagesImport
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\MessagesImportRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MessagesImport
{
	
	/**
	 * @ORM\OneToMany(targetEntity="Message", mappedBy="messages_import")
	 */
	 private $messages;
	 
	 /**
	 * @ORM\OneToMany(targetEntity="Classification", mappedBy="messages_import")
	 */
	 private $classifications;
	 
	 /**
	 * @ORM\OneToMany(targetEntity="Storage", mappedBy="messages_import")
	 */
	 private $storages;
	
	 /**
	 * @ORM\ManyToOne(targetEntity="Pack", inversedBy="messages_imports", cascade={"persist"})
	 * @ORM\JoinColumn(name="pack_id", referencedColumnName="id")
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
     * @var string
     *
     * @ORM\Column(name="original_filename", type="string", length=255)
     */
    private $original_filename;

    /**
     * @var string
     *
     * @ORM\Column(name="filepath", type="string", length=255)
     */
    private $filepath;

    /**
     * @var integer
     *
     * @ORM\Column(name="pack_id", type="integer")
     */
    private $pack_id;

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
     * @Assert\File
     */
	public $file;

	public function __construct()
	{
		$this->messages = new \Doctrine\Common\Collections\ArrayCollection();
		$this->storages = new \Doctrine\Common\Collections\ArrayCollection();
	}

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
     * Set original_filename
     *
     * @param string $originalFilename
     * @return MessagesImport
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->original_filename = $originalFilename;
    
        return $this;
    }

    /**
     * Get original_filename
     *
     * @return string 
     */
    public function getOriginalFilename()
    {
        return $this->original_filename;
    }

    /**
     * Set filepath
     *
     * @param string $filepath
     * @return MessagesImport
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
     * Set pack_id
     *
     * @param integer $packId
     * @return MessagesImport
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
     * Set created_by
     *
     * @param integer $createdBy
     * @return MessagesImport
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
	
	public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
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
	
	protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
	
	protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/messages_imports';
    }
	
	/**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
	
	public function upload()
	{
		if($this->file === null)return;
			
		$this->setOriginalFilename($this->file->getClientOriginalName());
		
	    // use the original file name here but you should
	    // sanitize it at least to avoid any security issues
		
		$filename = time() . '_' . $this->file->getClientOriginalName();
		
	    // move takes the target directory and then the
	    // target filename to move to
	    $this->file->move(
	        $this->getUploadRootDir(),
	        $filename
	    );
	
	    // set the path property to the filename where you've saved the file
	    $this->filepath = $filename;
	
	    // clean up the file property as you won't need it anymore
	    $this->file = null;
	}
	
	public function buildMessages()
	{
		$messages = array();
		$file = fopen($this->getAbsolutePath(),"r");
		$headers = fgetcsv($file,0,";");
		//remove the unused Translation column if it is there
		if($headers[count($headers)-1]=='Translation')array_pop($headers);
		while($row = fgetcsv($file,0,";"))
		{
			//index row with the headers!
			$row = array_combine(array_values($headers), array_values($row));
			
			//build the message
			$message = new Message();
			$message->setMkey($row['Array Key']);
			$message->setText($row['English String']);
			$message->setType("STRING");
			
			$m = array();
			preg_match("/^(?:\s*\d+\s*-\s*)?(.*?)\s*$/",$row['Section'],$m);
			$category = $m[1];
			
			//virtual properties
			$message->classification_data = array(
				"category" => $category,
				"section"  => $row['Group'],
				"subsection" => ''
			);
			
			$message->storage_data = array(
				'method'   => $row['Array Name'] ? 'ARRAY' : 'FILE',
				'path'     => str_replace('/en/', '/[iso]/', str_replace('/en.php','/[iso].php', $row['Storage File Path'])),
				'category' => $category,
				'custom'   => $row['Array Name']
			);
			
			$messages[] = $message;
			
		}
		fclose($file);
		return $messages;
	}
	

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return MessagesImport
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
     * @return MessagesImport
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

	

    /**
     * Add messages
     *
     * @param \FM\SymSlateBundle\Entity\Message $messages
     * @return MessagesImport
     */
    public function addMessage(\FM\SymSlateBundle\Entity\Message $messages)
    {
        $this->messages[] = $messages;
    
        return $this;
    }

    /**
     * Remove messages
     *
     * @param \FM\SymSlateBundle\Entity\Message $messages
     */
    public function removeMessage(\FM\SymSlateBundle\Entity\Message $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Add classifications
     *
     * @param \FM\SymSlateBundle\Entity\Classification $classifications
     * @return MessagesImport
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
     * Set pack
     *
     * @param \FM\SymSlateBundle\Entity\Pack $pack
     * @return MessagesImport
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
     * Add storages
     *
     * @param \FM\SymSlateBundle\Entity\Storage $storages
     * @return MessagesImport
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