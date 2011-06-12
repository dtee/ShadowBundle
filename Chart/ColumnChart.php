<?php
namespace Odl\ShadowBundle\Chart;

class ColumnChart extends Chart
{

    public function getChartOptions()
    {
        $options = parent::getChartOptions();
        $options['plotOptions'] = array(
                'column' => array(
                        'stacking' => 'percent'
                )
        );

        $options['tooltip'] = array(
                'enabled' => false
        );

        $options['xAxis'] = array(
                'categories' => $this->categories
        );

        $options['yAxis'] = array(
                'min' => 0,
                'text' => array(
                        'title' => 'Total games played'
                )
        );

        $options['chart']['defaultSeriesType'] = 'column';
        $options['chart']['zoomType'] = 'x';

        return $options;
    }
}