<?php

namespace App\Model;

class Task
{
	protected $id;

	protected $title;

	protected $content;

	protected $created_at;

	protected $updated_at;

	protected $expiry_at;

	protected $category;

	protected $author;

	protected $status;

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}
	
	public function getContent()
	{
		return $this->content;
	}

	public function setCreatedAt($created_at)
	{
		$this->created_at = $created_at;
	}
	
	public function getCreatedAt()
	{
		return $this->created_at;
	}

	public function setUpdatedAt($updated_at)
	{
		$this->updated_at = $updated_at;
	}
	
	public function getUpdatedAt()
	{
		return $this->updated_at;
	}

	public function setExpiryAt($expiry_at)
	{
		$this->expiry_at = $expiry_at;
	}
	
	public function getExpiryAt()
	{
		return $this->expiry_at;
	}

	public function setCategory($category)
	{
		$this->category = $category;
	}
	
	public function getCategory()
	{
		return $this->category;
	}

	public function setAuthor($author)
	{
		$this->author = $author;
	}

	public function getAuthor()
	{
		return $this->author;
	}

	public function setStatus($status)
	{
		$this->status = $status;
	}

	public function getStatus()
	{
		return $this->status;
	}
}

?>