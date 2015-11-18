<?php
/**
 * Created by PhpStorm.
 * User: jam
 * Date: 10.10.15
 * Time: 11:09
 */

namespace Trejjam\Utils\Tree;

use Nette,
	Trejjam;

abstract class AItem implements IItem
{
	protected $properties;
	protected $id       = NULL;
	protected $parentId = NULL;

	/**
	 * @var static[]
	 */
	protected $child = [];
	/**
	 * @var static
	 */
	protected $parent = NULL;

	/**
	 * AItem constructor.
	 * @param array|\stdClass|Nette\Database\Table\IRow $properties
	 * @param array|NULL                                $persistProperties
	 */
	public function __construct($properties, array $persistProperties = NULL)
	{
		$this->properties = $properties;

		if (!is_null($persistProperties)) {
			foreach ($persistProperties as $k => $v) {
				$keyName = Nette\Utils\Validators::isNumericInt($k) ? $v : $k;

				$this->$keyName = is_callable($v) ? $v($properties) : $v;
			}
		}
		else {
			$this->id = !is_object($properties) && isset($properties['id']) ? $properties['id'] : (isset($properties->id) ? $properties->id : NULL);
			$this->parentId = !is_object($properties) && isset($properties['parent_id']) ? $properties['parent_id'] : (isset($properties->parent_id) ? $properties->parent_id : NULL);
			if (is_null($this->parentId)) {
				$this->parentId = !is_object($properties) && isset($properties['parentId']) ? $properties['parentId'] : (isset($properties->parentId) ? $properties->parentId : NULL);
			}
		}
	}

	/**
	 * @param IItem[] $allItems
	 *
	 * @internal
	 */
	public function connectToParent(array $allItems)
	{
		if (!$this->hasParent()) return;
		$this->parent = $allItems[$this->parentId];
		$this->parent->connectChild($this);
	}
	/**
	 * @param IItem $child
	 *
	 * @internal
	 */
	public function connectChild(IItem $child)
	{
		if (!is_null($child->getId())) {
			$this->child[$child->getId()] = $child;
		}
	}

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return bool
	 */
	public function hasParent()
	{
		return !is_null($this->parentId);
	}
	/**
	 * @return bool
	 */
	public function hasChild()
	{
		return (bool)count($this->child);
	}

	/**
	 * @return null|static
	 */
	public function getParent()
	{
		return $this->parent;
	}
	/**
	 * @return static[]
	 */
	public function getChild()
	{
		return $this->child;
	}

	/**
	 * @return static[]
	 */
	public function createRootWay()
	{
		/** @var static[] $way */
		$way = [];

		if ($this->hasParent()) {
			$parent = $this->getParent();

			/** @var static[] $way */
			$way = $parent->createRootWay();
		}

		$way[] = $this;

		return $way;
	}

	/**
	 * @return array|\stdClass|Nette\Database\Table\IRow
	 */
	public function getProperties()
	{
		return $this->properties;
	}

	public function __get($name)
	{
		$properties = $this->getProperties();

		if (!is_object($properties) && isset($properties[$name])) {
			return $properties[$name];
		}
		else if (isset($properties->$name)) {
			return $properties->$name;
		}

		$trace = debug_backtrace();
		trigger_error(
			'Undefined property via __get(): ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_NOTICE);

		return NULL;
	}

	public function __isset($name)
	{
		$properties = $this->getProperties();

		if (!is_object($properties) && isset($properties[$name])) {
			return TRUE;
		}
		else if (isset($properties->$name)) {
			return TRUE;
		}

		return FALSE;
	}
}