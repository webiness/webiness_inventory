<?php
/**
 * WsChart
 * Is class for drawing animated and interactive charts. It uses Chart.js in the
 * background.
 *
 * @var integer $width Chart width
 * @var integer $height Chart height
 * @var array $options Global Chart.js options
 * @var array $attributes HTML Canvas attributes
 *
 */
class WsChart
{
    /**
     * @var array $_labels Chart labels
     */
    protected $_labels = array();
    /**
     * @var array $_datasets Chart datasets
     */
    protected $_datasets = array();
    /**
     * @var array $_options Global Chart.js options
     */
    protected $_options = array();
    /**
     * @var array $_attributes HTML canvas attributes
     */
    protected $_attributes = array();
    /**
     * @var string $_id ID of canvas where chart will be drawn
     */
    protected $_id = '';
    /**
     * @var string $_chart Chart object
     */
    protected $_chart = '';
    /**
     * @var integer $_width Chart width
     */
    protected $_width;
    /**
     * @var integer $_height Chart height
     */
    protected $_height;
    /**
     *@var string #_type Chart type
     */
    protected $_type = 'Line';


    public function __construct($width='', $height='',
            $options=array(), $attributes=array())
    {
        $this->_id = uniqid('WsChart_');

        // Always save canvas attributes as array
        if ($attributes && !is_array($attributes)) {
            $attributes = array($attributes);
        }
        $this->_attributes = $attributes;

        // global Chart.js options
        if (!empty($options)) {
            $this->_options = $options;
        }

        $this->_width = intval($width);
        $this->_height = intval($height);
    }


    public function __toString()
    {
        $this->renderChart();
        return $this->_chart;
    }


    /**
     * show chart
     *
     */
    public function show()
    {
        $this->renderChart();
        echo $this->_chart;
    }


    /**
     * add labels for chart
     *
     * @var array/string $labels List of labels or single label to add
     * @var boolean $reset Create new list of labels or append to existing
     *
     */
    public function addLabels($label, $reset=false)
    {
        // new list of labels
        if ($reset) {
            $this->_labels = array();
        }

        if (is_array($label)) {
            $this->_labels = array_merge($this->_labels, $label);
        } else {
            array_push($this->_labels, $label);
        }
    }


    /**
     * add datasets to chart
     *
     * @var array $dataset Dataset to add
     */
    public function addDataset($dataset, $reset=null)
    {
        // new list of dataset
        if ($reset) {
            $this->_datasets = array();
        }

        if (is_array($dataset)) {
            array_push($this->_datasets, $dataset);
        }
    }


    private function renderChart()
    {
        // canvas attributes
        $attributes = '';
        foreach ($this->_attributes as $attribute => $value) {
            $attributes .= ' '.$attribute.'="'.$value.'"';
        }

        // canvas width
        $width = '';
        if ($this->_width) {
            $width = ' width="' . $this->_width . '"';
        }

        // canvas height
        $height = '';
        if ($this->_height) {
            $height = ' height="' . $this->_height . '"';
        }

        // HTML canvas that will store chart
        $this->_chart = '<canvas id="'
                .$this->_id
                .'" style="border: 1px solid black;" '.$height.$width.$attributes
                .'></canvas>';

        // begining of chart script
        $this->_chart .= '<script language="javascript">';

        // prepare chart data
        $data = '';
        foreach ($this->_datasets as $dataset) {
            $data .= '{';
            $separator = '';

            // check for background color
            if (!array_key_exists('backgroundColor', $dataset)) {
                $data .= 'backgroundColor: randomColor(),';
            }
            foreach ($dataset as $key=>$val) {
                $data .= $separator.$key.': ';

                if (is_int( $val )) {
                    $data .= $val;
                } elseif (is_string($val)) {
                    $data .= '"'.str_replace('"', '\"', $val).'"';
                } elseif (is_bool($val)) {
                    $data .= $val ? 'true' : 'false';
                } elseif (is_array($val)) {
                    $data .= json_encode($val);
                } else {
                    $data .= $val;
                }
                $separator = ', ';
            }
            $data .= '},';
        }


        // chart data start
        $this->_chart .= 'var '.$this->_id.'_data = {';
        // labels
        $this->_chart .= 'labels: '.json_encode($this->_labels).',';
        // datasets
        $this->_chart .= 'datasets: ['.$data.'],';
        // chart data end
        $this->_chart .= '};';

        // caller for Chart.js
        $this->_chart .= 'var ctx_'.$this->_id
                .' = document.getElementById("'.$this->_id.'").getContext("2d");'
                .'var '.$this->_id.' = new Chart.'.$this->_type
                .'(ctx_'.$this->_id.', {data: '.$this->_id.'_data});';

        // end of chart script
        $this->_chart .= '</script>';
    }


    /**
     * set chart type
     *
     * @var string $type Chart type
     *
     */
    public function setType($type)
    {
        $accepted_types = array(
          'Line',
          'Bar',
          'Radar',
          'PolarArea',
          'Pie',
          'Doughnut'
        );

        if (in_array($type, $accepted_types)) {
            $this->_type = $type;
        }
    }
}
