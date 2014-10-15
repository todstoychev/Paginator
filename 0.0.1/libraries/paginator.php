<?php

class paginator {

    private $ci;
    private $model;
    private $count;

    public function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->helper('url');
    }

    /**
     * Sets the model to work with
     * 
     * @param string $model Model class name
     */
    public function set_model($model) {
        $this->model = $this->ci->load->model($model);
    }

    /**
     * Creates the table header
     * 
     * @param array $columns Array of columns. 
     * Format @example 
     * array('<column_name_in_the_table>' => array('<column_name_to_show>', [<sortable>]))
     * 'sortable' can be true or false
     * @param string $order Order direction
     * @param string $uri URI to the controller
     */
    public function set_table_header($columns, $items_per_page = 10, $page = 1, $order = null, $uri = null) {
        echo '<tr>';
        foreach ($columns as $key => $value) {
            echo '<th>';
            if (isset($value[1])) {
                $this->th($key, $value[0], $value[1], $items_per_page, $page, $order, $uri);
            } else {
                $this->th($key, $value[0], $items_per_page, $page, $order, $uri);
            }
            echo '</th>';
        }
        echo '</tr>';
    }

    /**
     * Sets the translation file. Can accept array of filenames
     * 
     * @param mixed $filenames Language filename
     * @param string $language Language
     */
    public function set_language_files($filenames, $language) {
        if (is_array($filenames)) {
            foreach ($filenames as $filename) {
                $this->ci->lang->load($filename, $language);
            }
        } else {
            $this->ci->lang->load($filename, $language);
        }
    }

    /**
     * Translates key if exists, else echoes the key
     * 
     * @param string $str Translation key
     */
    private function trans($str) {
        if (function_exists('lang')) {
            return lang($str);
        } else {
            return $str;
        }
    }

    /**
     * Creates th cell for the table header
     * 
     * @param string $column_name database column name
     * @param string $column Column name to show
     * @param bollean $sortable Indicates column as sortable
     * @param string $order Order direction
     * @param string $uri URI to the controller
     */
    private function th($column_name, $column, $sortable = false, $items_per_page = 10, $page = 1, $order = null, $uri = null) {
        
        if (isset($sortable) && $sortable != false) {
            echo '<a href="' . base_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . $page . '/' . $this->order($order) . '/' . $column_name) . '">' . $this->trans($column) . '</a>';
        } else {
            echo $this->trans($column);
        }
    }

    public function query($query, $table, $items_per_page, $page, $order, $param) {
        $order = $this->order($order);
        
        // Calculate the offset
        if ($page == 1) {
            $offset = 0;
        } else {
            $offset = ($page - 1)  * $items_per_page;
        }

        if ($this->ci->input->post('items_per_page')) {
            $items_per_page = $this->ci->input->post('items_per_page');
        }

        if (is_object($query) && get_class($query) == 'CI_DB_mysql_driver') {

            if ($param != null) {
                $query = $query->order_by($param, $order);
            }

            $count = $query;

            $this->count = count($count->get($table)->result());

            $result = $query->limit($items_per_page, $offset)
                    ->get($table);
            
            return $result->result();
        } elseif (is_string($query)) {
            $CI =& get_instance();

            if ($param != null) {
                $query .= " ORDER BY " . $param . " " . $order;
            }
            
            $this->count = $CI->db->query($query)->num_rows;
            
            $query .= " LIMIT " . $items_per_page . " OFFSET " . $offset;
            
            $result = $CI->db->query($query);
            
            return $result->result();
        }
    }

    /**
     * Changes the order direction
     * 
     * @param string $order Order direction
     * @return string Order direction
     */
    private function order($order) {
        if ($order == 'desc' || $order == 'DESC') {
            return $order = 'asc';
        } else {
            return $order = 'desc';
        }
    }

    /**
     * Creates the select dropdown for items per page
     * 
     * @param integer $per_page Items per page
     */
    public function get_items_per_page_dropdown($per_page) {
        $items_per_page = $this->ci->config->item('items_per_page');

        echo '<div class="form-group"><select name="items_per_page">';
        foreach ($items_per_page as $key => $value) {
            if ($key == $per_page) {
                echo '<option value="' . $key . '" selected="selected">';
            } else {
                echo '<option value="' . $key . '">';
            }
            echo $value;
            echo '</option>';
        }
        echo '</select></div>';
    }

    /**
     * Creates the pagination links
     * 
     * @param integer $items_per_page Items per page
     * @param string $uri URI to the controller action
     * @param integer $page Page number
     * @param string $order Order direction
     * @param string $param Column from the database to sort
     */
    public function get_pagination($items_per_page, $uri, $page, $order = null, $param = null) {
        $pages = ceil($this->count / $items_per_page);

        // First page link
        if ($page > 1 && $pages > 1) {
            echo '<ul class="pagination">';
            echo '<li><a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/1/' . $order . '/' . $param) . '">&laquo;</a></li>';
            echo '<li><a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . ($page - 1) . '/' . $order . '/' . $param) . '">&lsaquo;</li>';
        } elseif ($pages > 1 && $page == 1) {
            echo '<ul class="pagination">';
            echo '<li class="disabled"><a href="#">&laquo;</a></li>';
            echo '<li class="disabled"><a href="#">&lsaquo;</li>';
        }

        if ($pages > 13) {

            $this->first_three($items_per_page, $uri, $page, $order, $param, $pages);

            $this->middle($items_per_page, $uri, $page, $order, $param, $pages);

            $this->last_three($items_per_page, $uri, $page, $order, $param, $pages);
        } elseif ($pages > 1 && $pages <= 13) {
            for ($i = 1; $i <= $pages; $i++) {
                $this->active($i, $page);
                echo '<a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . $i . '/' . $order . '/' . $param) . '">' . $i . '</a>';
                echo '</li>';
            }
        }

        // Last page link and previous page link
        if ($page == $pages && $pages > 1) { 
            echo '<li class="disabled"><a href="#">&rsaquo;</a></li>';
            echo '<li class="disabled"><a href="#">&raquo;</a></li>';
            echo '</ul>';
        } elseif ($pages > 1) {
            echo '<li><a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . ($page + 1) . '/' . $order . '/' . $param) . '">&rsaquo;</a></li>';
            echo '<li><a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . $pages . '/' . $order . '/' . $param) . '">&raquo;</a></li>';
            echo '</ul>';
        }
    }

    /**
     * Creates first three links
     * 
     * @param integer $items_per_page Items per page
     * @param string $uri URI to the controller action
     * @param integer $page Current page
     * @param string $order Order direction
     * @param string $param Column from the database to sort
     */
    private function first_three($items_per_page, $uri, $page, $order, $param) {
        for ($i = 1; $i <= 3; $i++) {
            $this->active($i, $page);
            echo '<a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . $i . '/' . $order . '/' . $param) . '">' . $i . '</a>';
            echo '</li>';
        }
    }

    /**
     * Creates the last three links
     * 
     * @param integer $items_per_page Items per page
     * @param string $uri URI to the controller action
     * @param integer $page Current page
     * @param string $order Order direction
     * @param string $param Column from the database to sort
     */
    private function last_three($items_per_page, $uri, $page, $order, $param, $pages) {
        for ($i = $pages - 2; $i <= $pages; $i++) {
            $this->active($i, $page);
            echo '<a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . $i . '/' . $order . '/' . $param) . '">' . $i . '</a>';
            echo '</li>';
        }
    }

    /**
     * Creates middle pagination links for the pager
     * 
     * @param integer $items_per_page Items per page count
     * @param string $uri URI to controller
     * @param integer $page Current page
     * @param string $order Order direction
     * @param string $param Column name to sort
     * @param integer $pages Total number of pages
     */
    private function middle($items_per_page, $uri, $page, $order, $param, $pages) {
        if ($page >= 1 && $page <= 6) {
            echo '<li class="disabled"><a href="#">...</a></li>';
            for ($i = 4; $i < 9; $i++) {
                $this->active($i, $page);
                echo '<a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . $i . '/' . $order . '/' . $param) . '">' . $i . '</a>';
                echo '</li>';
            }
            echo '<li class="disabled"><a href="#">...</a></li>';
        } elseif ($page > 6 && $page <= $pages - 6) {
            echo '<li class="disabled"><a href="#">...</a></li>';
            for ($i = $page - 2; $i < $page + 3; $i++) {
                $this->active($i, $page);
                echo '<a href="' . site_url() . str_replace('//', '/',  $uri . '/' . $items_per_page . '/' . $i . '/' . $order . '/' . $param) . '">' . $i . '</a>';
                echo '</li>';
            }
            echo '<li class="disabled"><a href="#">...</a></li>';
        } elseif ($page <= $pages) {
            echo '<li class="disabled"><a href="#">...</a></li>';
            for ($i = $pages - 7; $i < $pages - 2; $i++) {
                $this->active($i, $page);
                echo '<a href="' . site_url() . str_replace('//', '/', $uri . '/' . $items_per_page . '/' . $i . '/' . $order . '/' . $param) . '">' . $i . '</a>';
                echo '</li>';
            }
            echo '<li class="disabled"><a href="#">...</a></li>';
        } else {
            echo '<li class="disabled"><a href="#">...</a></li>';
        }
    }

    /**
     * Puts the bootstrap 'active' css class
     * 
     * @param integer $i Count
     * @param integer $page Current page
     */
    private function active($i, $page) {
        if ($i == $page) {
            echo '<li class="active">';
        } else {
            echo '<li>';
        }
    }

}
