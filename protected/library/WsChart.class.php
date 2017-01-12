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
     *
     */
    protected $_labels = array();
    /**
     * @var array $_datasets Chart datasets
     *
     */
    protected $_datasets = array();
    /**
     * @var array $_datasetsLabels Labels for datasets
     *
     */
    protected $_datasetsLabels = array();
    /**
     * @var array $_options Global Chart.js options
     *
     */
    protected $_options = array();
    /**
     * @var array $_attributes HTML canvas attributes
     *
     */
    protected $_attributes = array();
    /**
     * @var string $_id ID of canvas where chart will be drawn
     *
     */
    protected $_id = '';
    /**
     * @var string $_chart Chart object
     *
     */
    protected $_chart = '';
    /**
     * @var integer $_width Chart width
     *
     */
    protected $_width;
    /**
     * @var integer $_height Chart height
     *
     */
    protected $_height;
    /**
     *@var string #_type Chart type
     *
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
     *
     */
    public function addDataset($dataset, $label ='', $reset=null)
    {
        // new list of dataset
        if ($reset) {
            $this->_datasets = array();
            $this->_datasetsLabels = array();
        }

        if (is_array($dataset)) {
            array_push($this->_datasets, $dataset);
            array_push($this->_datasetsLabels, $label);
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

        $label_index = 0;

        // prepare dataset
        $data = '';
        foreach ($this->_datasets as $dataset) {
            $data .= '{';

            // random color for background
            $data .= 'backgroundColor: randomColor(), ';

            // labels
            $data .= 'label: "'.$this->_datasetsLabels[$label_index].'", ';
            $label_index++;

            // chart data values
            $data .= 'data: [';
            foreach ($dataset as $key=>$val) {


                if (is_int( $val )) {
                    $data .= $val;
                } elseif (is_string($val)) {
                    $data .= '"'.str_replace('"', '\"', $val).'"';
                } else {
                    $data .= $val;
                }
                $data .= ', ';
            }
            $data .= ']}';
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
                .' = document.getElementById("'.$this->_id.'");'
                .'var '.$this->_id.' = new Chart('.'ctx_'.$this->_id
                .', {type: "'.$this->_type.'", data: '.$this->_id.'_data,'
                .'options: {scales: {yAxes: [{ticks: {beginAtZero:true}}]}}'
                .'});';

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
          'line',
          'bar',
          'radar',
          'polarArea',
          'pie',
          'doughnut'
        );

        if (in_array($type, $accepted_types)) {
            $this->_type = $type;
        }
    }
}

