<?php
namespace Odl\ShadowBundle\Chart;

class Chart {
	protected $title;
	protected $subTitle;
	protected $categories;
	protected $series;

	public $options = array(
		'line' => array(
			'dataLabels' => array ('enabled' => true),
			'enableMouseTracking' => false
		),
	);

	public function __construct($title, array $data)
	{
		if (empty($data))
		{
			throw new \Exception("Data must not be empty");
		}

		$this->title = $title;

		foreach ($data as $key => $values)
		{
			$serie = array();
			$serie['name'] = $key;
			$serie['data'] = $values;
			$this->series[] = $serie;

			if (!$this->categories)
			{
				$this->categories = array_keys($values);
			}
		}
	}

	/**
	 * @return the $title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return the $subTitle
	 */
	public function getSubTitle()
	{
		return $this->subTitle;
	}

	/**
	 * @return the $series
	 */
	public function getSeries()
	{
		return $this->series;
	}

	/**
	 * @return the $categories
	 */
	public function getCategories()
	{
		return $this->categories;
	}

	/**
	 * @return the $options
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param field_type $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @param field_type $subTitle
	 */
	public function setSubTitle($subTitle)
	{
		$this->subTitle = $subTitle;
	}

	/**
	 * @param field_type $series
	 */
	public function setSeries($series)
	{
		$this->series = $series;
	}

	/**
	 * @param field_type $categories
	 */
	public function setCategories($categories)
	{
		$this->categories = $categories;
	}

	/**
	 * @param field_type $options
	 */
	public function setOptions($options)
	{
		$this->options = $options;
	}

}
