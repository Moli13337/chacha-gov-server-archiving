<?php
namespace App\Criteria;


use Prettus\Repository\Contracts\CriteriaInterface;

abstract class BaseCriteria implements CriteriaInterface
{
	/**
	 * 注入过滤数组
	 *
	 * @var array
	 */
	protected $params;

	protected $key;

	protected $operator;

	public function __construct(array $params = null, $key = null, $operator = null)
	{
		$this->params = $params;
		$this->key = $key;
		$this->operator = $operator;
	}

}