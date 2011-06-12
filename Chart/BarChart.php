<?php
namespace Odl\ShadowBundle\Chart;

class BarChart extends Chart
{

    public function getChartOptions()
    {
        $options = parent::getChartOptions();
        $options['plotOptions'] = array(
                'series' => array(
                        'stacking' => 'normal'
                )
        );

        $options['tooltip'] = array(
                'enabled' => true
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

        $options['chart']['defaultSeriesType'] = 'bar';
        $options['chart']['zoomType'] = 'x';

        return $options;
    }
}