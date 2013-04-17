<?php

namespace FM\SymSlateBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

require 'Archive/Tar.php';

/**
 * TranslationsImport
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FM\SymSlateBundle\Entity\TranslationsImportRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TranslationsImport
{
	/**
	 * @ORM\OneToMany(targetEntity="Translation", mappedBy="translations_import")
	 */
	private $translations;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="translations_imports")
	 * @ORM\JoinColumn(name="created_by", referencedColumnName="id", onDelete="CASCADE")
	 */
	private $creator;
	
	public function __construct()
	{
		$this->translations = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return TranslationsImport
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
     * @return TranslationsImport
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
     * Set created_by
     *
     * @param integer $createdBy
     * @return TranslationsImport
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
     * @return TranslationsImport
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return TranslationsImport
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    
        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
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
        return 'uploads/translations_imports';
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
		if($this->file === null)return false;

        if(!preg_match('/(?:\.gzip|\.tar\.gz|\.zip)$/',$this->file->getClientOriginalName()))
        {
            return false;
        }
			
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

        return true;
	}
	

    /**
     * Add translations
     *
     * @param \FM\SymSlateBundle\Entity\Translation $translations
     * @return TranslationsImport
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
	
	public function buildTranslations($version=null, $logger=null)
	{
		
		$translations = array();
		

$exp = <<<'NOW'
/\w+\s*\[\s*'(.*?[^\\])'\s*]\s*=\s*'(.*?[^\\])'\s*;\s*(?:$|\n)/
NOW;

//non mail non front office     
$nf_exp = <<<'NOW'
/translations\/([a-z]{2})\/(?:admin\.php|errors\.php|fields\.php|pdf\.php|tabs\.php)$/
NOW;

//front office
$f_exp = <<<'NOW'
/themes\/\w+\/lang\/([a-z]{2})\.php$/
NOW;

//module translation, non mail
$m_exp = <<<'NOW'
/modules\/\w+(?:\/translations)?\/([a-z]{2})\.php$/
NOW;

//mail objects
$o_exp = <<<'NOW'
/mails\/([a-z]{2})\/lang.php$/
NOW;

$ma_exp = <<<'NOW'
/mails\/([a-z]{2})\/([^\.]+)\.(txt|html)$/
NOW;

		$arch  = new \Archive_Tar($this->getAbsolutePath());
		$files = $arch->listContent();
		$total = 0;

        $dir=tempnam(sys_get_temp_dir(),'symslate_timport');
        if(file_exists($dir)) { unlink($dir); }
        mkdir($dir);

        if($logger)$logger->info("Temp dir: " . $dir);

        $arch->extract($dir);

		foreach($files as $f)
		{
            $path = "$dir/".$f['filename'];
            if(!is_dir($path))
            {
                //if($logger)$logger->info("Parsing file: " . $f['filename']);
    			$data    = file_get_contents($path);
    						
    			$match = array();
    			if(null !== $version and preg_match($ma_exp, $f['filename'],$match))
    			{
    				$total += 1;
    				$translation = new Translation();
    				$translation->setText($data);
    				$translation->language_code = $match[1];
    				$translation->setMkey('mail:'.$version.':/'.str_replace("/{$match[1]}/", '/[iso]/', $f['filename']));
    				
    				$translations[]  = $translation;
    			}
    			else if(   preg_match($nf_exp,$f['filename'],$match) 
    			        or preg_match($f_exp ,$f['filename'],$match) 
    			        or preg_match($m_exp ,$f['filename'],$match) 
    			        or preg_match($o_exp ,$f['filename'],$match))
    			{
    				$matches = array();
    				$count   = preg_match_all($exp, $data, $matches);
    				$total  += $count;
    				for($i = 0; $i < $count; $i++)
    				{
    					$translation = new Translation();
    					$translation->setText($matches[2][$i]);
    					$translation->language_code = $match[1];
    					$translation->setMkey($matches[1][$i]);
    					
    					$translations[] = $translation;
    				}
    			}
    			else
    			{
    				
    			}
            }
		}
        

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

		return $translations;
	}
	

    

    /**
     * Set creator
     *
     * @param \FM\SymSlateBundle\Entity\User $creator
     * @return TranslationsImport
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
}