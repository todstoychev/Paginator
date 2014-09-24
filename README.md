Paginator is a Codeigniter spark used to provide pagination.
Paginator has a dropdown select for the items per page functionality. And it is necessary to use jQuery in case you want to use it.
Paginator has a table_header which provides the table header for the data. Also sortable columns are available and Bootstrap 3 support.  

#Installation
Download or clone the project in your codegniter_project/sparks directory.
Rename the base directory from Paginator-master to Paginator if necessary.
In your codeigniter_project/application/config/autoload.php declare the usage of this spark:
    
    $['autoload'] = ['Paginator/x.x.x'];

#Basic usage
##Controller:
    
    public function my_method($items_per_page = 10, $page = 1, $order_direction = 'desc', $param = null) {
        
        // Paginator functions

        // Set the model to use
        set_model('my_model');
        
        // Set translation file/files
        // This helper can accept array of filenames
        set_translation_file('translation_file', 'language');

        // Creates the query and returns the data for the database.
        // As first argument put your initial query.
        // Second argument is the part from the uri that represents your 
        // controller name. 
        
        $data = get_data($this->db->where(['param1' => 'something1', 'param2' => 'something2']), 'table_name', $items_per_page, $page, $order_direction, $param);

        $this->load->view('template', ['data' => $data, 'order_direction' => $order_direction, 'items_per_page' => (int)$items_per_page, 'page' => $page, 'param' => $param]);
    }
    
    where

Template helpers: 

For each one helper the uri parameter must be in the following format:
'/controller_name/action_name' 

=========================
	table_header($columns, $items_per_page = 10, $page = 1, $order = 'desc', $uri = null)

Accepts array of columns in the format:
array('<column_name_in_the_table>' => array('<column_name_to_show>', [<sortable>])) .
Parameter 'sortable' can be true or false and it is optional

Example:
	<table>
	    <thead>
	        <?php
	        table_header($columns, $items_per_page = 10, $page = 1, $order = 'desc', $uri = null);
	        ?>
	    </thead>
	    <tbody>
	        ...
	    </tbody>
	</table>

=========================
	items_per_page($uri, $items_per_page, $attr = [])

Creates a dropdown with items per values. Those values can be set in the spark's
config file. See $config['items_per_page'] .
First argument got to be this part from the uri that represents the controller 
name. If your route is http://example.com/my_controller/my_action, put as first
argument 'my_controller'.
The secont argument is the current items per page value.
Third argument is array with attributes for the items per page form, 
e.g. ['class' => 'my_css_class', 'id' => 'my_css_id'].

There is a javascript file that provides the functionality for the dropdown.
You can find it in the assets folder of the spark (Paginator/x.x.x/assets/js/paginator.js). 

========================
	pagination($items_per_page, $uri, $page = 1, $order = null, $param = null)

Creates pager links. The pager is Bootstrap 3 ready.

First argument is the current items per page value.
Second argument got to be this part from the uri that represents the controller 
and the action ('/controller_name/action') name. 
If your route is http://example.com/my_controller/my_action, put as first
argument 'my_controller'.
Third argument is the current page number.
Fourth argument represents the order direction - 'asc' or 'desc'.
Fifth argument is the database table column name that is sorted. 
