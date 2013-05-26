<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Pagination {

    /**
     * @var CI_Controller
     */
    private $CI;

    /**
     * Use a %d
     * @var string
     */
    protected $total_rows;
    protected $per_page;

    /**
     * Current page
     * @var int
     */
    protected $uri_segment = 3;
    protected $cur_page_class;

    /**
     * Show page numbers?
     * @var bool
     */
    protected $show_numbers = FALSE;

    /**
     * @var int
     */
    protected $num_links = 2;
    protected $base_url;
    protected $prev_class = 'prev';
    protected $next_class = 'next';
    protected $button_name = 'page';
    protected $button_type = 'submit';

    /**
     * 
     * @param array $params 
     */
    public function __construct($params = array()) {
        log_message('debug', 'Pagination __construct($params): ' . string_to_log(var_export($params, TRUE)));
        $this->CI = & get_instance();

        if ($this->CI->config->load('pagination', TRUE, TRUE)) {
            $params = array_merge($params, $this->CI->config->config['pagination']);
        }

        $this->inicialize($params);

        log_message('debug', __CLASS__ . " Class Initialized");
    }

    /**
     * @param array $params 
     */
    function inicialize($params = array()) {

        if (!empty($params))
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }
    }

    function total_rows() {
        return $this->total_rows;
    }

    function per_page() {
        return $this->per_page;
    }

    function offset() {
        $cur_page = $this->CI->uri->segment($this->uri_segment);
        if (!$cur_page)
            $cur_page = 1;

        /* @var $pages int */
        $pages = ceil($this->total_rows / $this->per_page);
        log_message('debug', __CLASS__ . " Library --> Pages = $pages.");

        # Garantindo que vá sempre exibir registros
        if ($cur_page > $pages)
            $cur_page = $pages;

        return (int) ($cur_page * $this->per_page) - $this->per_page;
    }

    function show_numbers() {
        $this->show_numbers = TRUE;
    }

    function num_links($value) {
        $this->num_links = (int) $value;
    }

    function hide_numbers() {
        $this->show_numbers = FALSE;
    }

    /**
     * @return string 
     */
    function links() {
        if (!$this->total_rows OR !$this->per_page)
            return '';

        $this->CI->load->language('pagination');

        if (empty($this->base_url))
            $this->base_url = site_url(array(
                $this->CI->router->fetch_class(),
                $this->CI->router->fetch_method(),
                '%d',
                    ));
        log_message('debug', __CLASS__ . " Library --> Rows = {$this->total_rows}.");

        /* @var $cur_page int */
        $cur_page = (int) $this->CI->uri->segment($this->uri_segment);
        if (empty($cur_page))
            $cur_page = 1;
        log_message('debug', __CLASS__ . " Library --> Current page = $cur_page.");

        /* @var $pages int */
        $pages = ceil($this->total_rows / $this->per_page);
        log_message('debug', __CLASS__ . " Library --> Pages = $pages.");

        /* @var $begin int */
        $begin = $cur_page - $this->num_links;
        if ($begin < 1)
            $begin = 1;
        log_message('debug', __CLASS__ . " Library --> Begin = $begin.");

        /* @var $end int */
        $end = $cur_page + $this->num_links;
        if ($end > $pages)
            $end = $pages;
        log_message('debug', __CLASS__ . " Library --> End = $end.");

        $output = '';

        if ($cur_page > 1) {
            $prev_page = $cur_page - 1;
            $output .= '<a ';
            if (!empty($this->prev_class)) {
                $output .= 'class="';
                $output .= $this->prev_class;
                $output .= '"';
            }
            $output .= 'href="';
            $output .= sprintf($this->base_url, $prev_page);
            $output .= '" ';
            $output .= 'title="';
            $output .= sprintf($this->CI->lang->line('page_n'), $prev_page);
            $output .= '">';
            $output .= $this->CI->lang->line('prev_page');
            $output .= '</a>';
        }

        if ($this->show_numbers)
            for ($page = $begin; $page <= $end; $page++) {
                $output .= "\n";
                if ($cur_page != $page) {
                    $output .= '<a ';
                    if (!empty($this->page_class)) {
                        $output .= 'class="';
                        $output .= $this->page_class;
                        $output .= '"';
                    }
                    $output .= 'href="';
                    $output .= sprintf($this->base_url, $page);
                    $output .= '" ';
                    $output .= 'title="';
                    $output .= sprintf($this->CI->lang->line('page_n'), $page);
                    $output .= '">';
                } elseif (!empty($this->cur_page_class)) {
                    $output .= '<span class="';
                    $output .= $this->cur_page_class;
                    $output .= '">';
                }
                $output .= $page;
                if ($cur_page != $page)
                    $output .= '</a>';
                elseif (!empty($this->cur_page_class))
                    $output .= '</span>';
            }

        $output .= "\n";

        if ($cur_page * $this->per_page < $this->total_rows) {
            $next_page = $cur_page + 1;
            $output .= '<a ';
            if (!empty($this->next_class)) {
                $output .= 'class="';
                $output .= $this->next_class;
                $output .= '"';
            }
            $output .= 'href="';
            $output .= sprintf($this->base_url, $next_page);
            $output .= '" title="';
            $output .= sprintf($this->CI->lang->line('page_n'), $next_page);
            $output .= '">';
            $output .= $this->CI->lang->line('next_page');
            $output .= '</a>';
        }

        return $output;
    }

    /**
     * Requires http://fortawesome.github.io/Font-Awesome/
     * @param string $alignment 'centered', 'right' or 'left'
     * @return string
     */
    function bootstrap_links($alignment = 'centered', $size = 'normal') {
        if (!$this->total_rows OR !$this->per_page)
            return '';

        $this->CI->load->language('pagination');

        if (empty($this->base_url))
            $this->base_url = site_url(array(
                $this->CI->router->fetch_class(),
                $this->CI->router->fetch_method(),
                '%d',
                    ));
        log_message('debug', __CLASS__ . " Library --> Rows = {$this->total_rows}.");

        /* @var $cur_page int */
        $cur_page = (int) $this->CI->uri->segment($this->uri_segment);
        if (empty($cur_page))
            $cur_page = 1;
        log_message('debug', __CLASS__ . " Library --> Current page = $cur_page.");

        /* @var $pages int */
        $pages = ceil($this->total_rows / $this->per_page);
        log_message('debug', __CLASS__ . " Library --> Pages = $pages.");

        # Garantindo que vá sempre exibir registros
        if ($cur_page > $pages)
            $cur_page = $pages;

        /* @var $begin int */
        $begin = $cur_page - $this->num_links;
        if ($begin < 1)
            $begin = 1;
        log_message('debug', __CLASS__ . " Library --> Begin = $begin.");

        /* @var $end int */
        $end = $cur_page + $this->num_links;
        if ($end > $pages)
            $end = $pages;
        log_message('debug', __CLASS__ . " Library --> End = $end.");

        $output = '';

        $prev_page = $cur_page - 1;
        $next_page = $cur_page + 1;

        if ($this->show_numbers) {
            $output = '<div class="pagination pagination-';
            $output .= $alignment;
            $output .= ' pagination-';
            $output .= $size;
            $output .= '">';
            $output .= '<ul>';
            $output .= "\n";

            $output .= '<li';
            if ($cur_page == 1)
                $output .= ' class="disabled"';
            $output .= '><a href="';
            if ($prev_page > 0)
                $output .= sprintf($this->base_url, $prev_page);
            else
                $output .= 'javascript:void(0);';
            $output .= '">&laquo;</a></li>';
            $output .= "\n";

            for ($page = $begin; $page <= $end; $page++) {
                $output .= "\n";
                $output .= '<li';
                if ($cur_page == $page)
                    $output .= ' class="active"';
                $output .= '><a href="';
                $output .= sprintf($this->base_url, $page);
                $output .= '" ';
                $output .= 'title="';
                $output .= sprintf($this->CI->lang->line('page_n'), $page);
                $output .= '">';
                $output .= $page;
                $output .= '</a></li>';
                $output .= "\n";
            }

            $output .= '<li';
            if ($cur_page * $this->per_page > $this->total_rows)
                $output .= ' class="disabled"';
            $output .= '><a href="';
            if ($cur_page * $this->per_page < $this->total_rows)
                $output .= sprintf($this->base_url, $next_page);
            else
                $output .= 'javascript:void(0);';
            $output .= '">&raquo;</a></li>';
            $output .= "\n";

            $output .= '</ul></div>';
        } else {
            $output = '<ul class="pager">';

            $output .= '<li class="previous';
            if ($cur_page == 1)
                $output .= ' disabled';
            $output .= '"><a href="';
            if ($prev_page > 0)
                $output .= sprintf($this->base_url, $prev_page);
            else
                $output .= 'javascript:void(0);';
            $output .= '" ';
            $output .= 'title="';
            $output .= sprintf($this->CI->lang->line('page_n'), $prev_page);
            $output .= '"><i class="icon-arrow-left"></i> ';
            $output .= $this->CI->lang->line('prev_page');
            $output .= '</a></li>';
            $output .= "\n";

            $output .= '<li class="next';
            if ($cur_page * $this->per_page > $this->total_rows)
                $output .= ' disabled';
            $output .= '"><a href="';
            if ($cur_page * $this->per_page < $this->total_rows)
                $output .= sprintf($this->base_url, $next_page);
            else
                $output .= 'javascript:void(0);';
            $output .= '" ';
            $output .= 'title="';
            $output .= sprintf($this->CI->lang->line('page_n'), $next_page);
            $output .= '">';
            $output .= $this->CI->lang->line('next_page');
            $output .= ' <i class="icon-arrow-right"></i></a></li>';
            $output .= "\n";

            $output .= '</ul>';
        }

        return $output;
    }

    function buttons() {
        if (!$this->total_rows OR !$this->per_page)
            return '';

        $this->CI->load->language('pagination');
        log_message('debug', __CLASS__ . " Library --> Rows = {$this->total_rows}.");

        /* @var $cur_page int */
        $cur_page = (int) $this->CI->uri->segment($this->uri_segment);
        if (empty($cur_page))
            $cur_page = 1;
        log_message('debug', __CLASS__ . " Library --> Current page = $cur_page.");

        /* @var $pages int */
        $pages = ceil($this->total_rows / $this->per_page);
        log_message('debug', __CLASS__ . " Library --> Pages = $pages.");

        /* @var $begin int */
        $begin = $cur_page - $this->num_links;
        if ($begin < 1)
            $begin = 1;
        log_message('debug', __CLASS__ . " Library --> Begin = $begin.");

        /* @var $end int */
        $end = $cur_page + $this->num_links;
        if ($end > $pages)
            $end = $pages;
        log_message('debug', __CLASS__ . " Library --> End = $end.");

        /* @var $template string */
        $template = '<button{class}{disabled} name="{name}" title="{title}" type="{type}" value="{value}">{label}</button>';
        /* @var $pattern array */
        $pattern = array(
            '/{class}/',
            '/{disabled}/',
            '/{name}/',
            '/{type}/',
            '/{title}/',
            '/{value}/',
            '/{label}/',
        );

        /* @var $output string */
        $output = '';

        if ($cur_page > 1) {
            /* @var $prev_page int */
            $prev_page = (int) $cur_page - 1;
            /* @var $prev_class string */
            $prev_class = '';
            if (!empty($this->prev_class))
                $prev_class = ' class="' . $this->prev_class . '"';
            $output .= preg_replace($pattern, array(
                $prev_class,
                '',
                $this->button_name,
                $this->button_type,
                sprintf($this->CI->lang->line('page_n'), $prev_page),
                $prev_page,
                $this->CI->lang->line('prev_page'),
                    ), $template);
        }

        if ($this->show_numbers)
            for ($page = $begin; $page <= $end; $page++) {
                /* @var $page int */

                $output .= "\n";

                /* @var $page_class string */
                $page_class = '';
                /* @var $disabled string */
                $disabled = '';

                if (!empty($this->page_class))
                    $page_class = ' class="' . $this->page_class . '"';
                if ($cur_page == $page AND !empty($this->cur_page_class))
                    $page_class = rtrim($page_class, '"') . ' ' . $this->cur_page_class . '"';
                if ($cur_page == $page)
                    $disabled = ' disabled="disabled"';
                $output .= preg_replace($pattern, array(
                    $page_class,
                    $disabled,
                    $this->button_name,
                    $this->button_type,
                    sprintf($this->CI->lang->line('page_n'), $page),
                    $page,
                    $page,
                        ), $template);
            }

        if ($cur_page * $this->per_page < $this->total_rows) {
            $output .= "\n";

            $next_page = $cur_page + 1;
            if (!empty($this->next_class))
                $next_class = ' class="' . $this->next_class . '"';
            $output .= preg_replace($pattern, array(
                $next_class,
                '',
                $this->button_name,
                $this->button_type,
                sprintf($this->CI->lang->line('page_n'), $next_page),
                $next_page,
                $this->CI->lang->line('next_page'),
                    ), $template);
        }

        return $output;
    }

}

?>
