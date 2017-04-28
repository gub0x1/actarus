<?php

namespace ArusEntityAttachmentBundle\Entity;


/**
 * Search
 */
class Search
{
	/**
	 * @var int
	 */
	private $page;
	
	/**
	 * @var int
	 */
    private $id;
	
	/**
	 * @var ArusProject
	 */
	private $project;
	
	/**
	 * @var int
	 */
	private $entityType;
	
	/**
	 * @var string
	 */
	private $entityId;
	
	/**
	 * @var string
	 */
	private $title;
	
	/**
	 * @var string
	 */
	private $filename = null;
	
	/**
	 * @var string
	 */
	private $realname = null;
	
	/**
	 * @var \DateTime
	 */
	private $minCreatedAt;
	
	/**
	 * @var \DateTime
	 */
	private $maxCreatedAt;
	
	
	public function __construct() {
		$this->page = 1;
	}
	
	
	/**
	 * Set page
	 *
	 * @param string $page
	 *
	 * @return Search
	 */
	public function setPage($page)
	{
		$this->page = $page;
		
		return $this;
	}
	
	/**
	 * Get page
	 *
	 * @return int
	 */
	public function getPage()
	{
		return $this->page;
	}
	
	
	/**
	 * Set id
	 *
	 * @param string $id
	 *
	 * @return Search
	 */
	public function setId($id)
	{
		$this->id = $id;
		
		return $this;
	}
	
	/**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
	 * Set project
	 *
	 * @param ArusProject $project
	 *
	 * @return Search
	 */
	public function setProject($project)
	{
		$this->project = $project;
		
		return $this;
	}
	
	/**
	 * Get project
	 *
	 * @return ArusProject
	 */
	public function getProject()
	{
		return $this->project;
	}
	
	/**
	 * Set entityType
	 *
	 * @param string $entityType
	 *
	 * @return Search
	 */
	public function setEntityType($entityType)
	{
		$this->entityType = $entityType;
		
		return $this;
	}
	
	/**
	 * Get entityType
	 *
	 * @return string
	 */
	public function getEntityType()
	{
		return $this->entityType;
	}
	
	/**
	 * Set entityId
	 *
	 * @param string $entityId
	 *
	 * @return Search
	 */
	public function setEntityId($entityId)
	{
		$this->entityId = $entityId;
		
		return $this;
	}
	
	/**
	 * Get entityId
	 *
	 * @return string
	 */
	public function getEntityId()
	{
		return $this->entityId;
	}
	
	/**
	 * Set title
	 *
	 * @param string $title
	 *
	 * @return Search
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		
		return $this;
	}
	
	/**
	 * Get title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	/**
	 * Set filename
	 *
	 * @param int $filename
	 *
	 * @return Search
	 */
	public function setFilename($filename)
	{
		$this->filename = $filename;
		
		return $this;
	}
	
	/**
	 * Get filename
	 *
	 * @return int
	 */
	public function getFilename()
	{
		return $this->filename;
	}
	
	/**
	 * Set realname
	 *
	 * @param string $realname
	 *
	 * @return Search
	 */
	public function setRealname($realname)
	{
		$this->realname = $realname;
		
		return $this;
	}
	
	/**
	 * Get realname
	 *
	 * @return int
	 */
	public function getRealname()
	{
		return $this->realname;
	}
	
	/**
     * Set minCreatedAt
     *
     * @param \DateTime $minCreatedAt
     *
     * @return Search
     */
    public function setMinCreatedAt($minCreatedAt)
    {
        $this->minCreatedAt = $minCreatedAt;

        return $this;
    }

    /**
     * Get minCreatedAt
     *
     * @return \DateTime
     */
    public function getMinCreatedAt()
    {
        return $this->minCreatedAt;
    }
	
	/**
     * Set maxCreatedAt
     *
     * @param \DateTime $maxCreatedAt
     *
     * @return Search
     */
    public function setMaxCreatedAt($maxCreatedAt)
    {
        $this->maxCreatedAt = $maxCreatedAt;

        return $this;
    }

    /**
     * Get maxCreatedAt
     *
     * @return \DateTime
     */
    public function getMaxCreatedAt()
    {
        return $this->maxCreatedAt;
    }
}
