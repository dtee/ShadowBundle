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

	public function __construct($title, array $data, array $categories)
	{
		if (empty($data))
		{
			throw new \Exception("Data must not be empty");
		}

		$this->title = $title;
		$this->categories = $categories;

		foreach ($data as $key => $values)
		{
			$serie = array();
			$serie['name'] = $key;
			$serie['data'] = $values;
			$this->series[] = $serie;
		}
	}

	/**
	 * @return the $title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	public function getId()
	{
		$key = str_replace(' ', '_', $this->title);
		$key = strtolower($key);

		return $key;
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

	public function getChartOptions()
	{
		$options = array(
			'chart' => array(
				'renderTo' => $this->getId(),
				'defaultSeriesType' => 'spline',
				'zoomType' => 'x',
			),
			'title' => array('text' => $this->getTitle()),
			'xAxis' => array(
				'maxZoom' => 10,
				'title' => 'Game #'
			),
			'yAxis' => array('title' => array('text' => 'Rate')),
			'tooltip' => array(
				'enabled' => true,
				'crosshairs' => true,
				'shared' => true,
				//'formatter' => null,
			),
			'legend' => array(
				'layout' => 'vertical',
				'align' => 'right',
				'verticalAlign' => 'top',
				'x' => -10,
				'y' => 100,
				'borderWidth' => 0
			),
			'plotOptions' => array (
				'line' => array(
					'dataLabels' => array('enabled' => true),
					'enableMouseTracking' => false
				),
				'spline' => array(
					'marker' => array(
						'enabled' => true,
						'radius' => 4,
						'lineColor' => '#666666',
						'lineWidth' => 1
					)
				)
			),
			'series' => $this->getSeries(),
			'credits' => array(
				'enabled' => false
			)
		);

		return $options;
	}

}
