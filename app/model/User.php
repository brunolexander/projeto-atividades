<?php

namespace App\Model;

class User
{
	protected $id;

	protected $name;

	protected $email;

	protected $permission;

	protected $created_at;

	protected $last_access;

	protected $num_tasks_created;

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setPermission($permission)
	{
		$this->permission = $permission;
	}

	public function getPermission()
	{
		return $this->permission;
	}

	public function setCreatedAt($created_at)
	{
		$this->created_at = $created_at;
	}

	public function getCreatedAt()
	{
		return $this->created_at;
	}

	public function setLastAccess($last_access)
	{
		$this->last_access = $last_access;
	}

	public function getLastAccess()
	{
		return $this->last_access;
	}

	public function setNumTasksCreated($num_tasks_created)
	{
		$this->num_tasks_created = $num_tasks_created;
	}

	public function getNumTasksCreated()
	{
		return $this->num_tasks_created;
	}
}

?>