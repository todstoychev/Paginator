Paginator is a Codeigniter spark used to provide pagination.
This spark is still beta and it is not well tested.
Paginator has a dropdown select for the items per page functionality. And it is necessary to use jQuery in case you want to use it. There is also a script in the assets forlder that handles the items per page form submission.
Paginator has a table_header helper which provides the table header for the data table. Also sortable columns are available and Bootstrap 3 support.  

You can test the project [here](http://paginator.todsto.eu).

# Installation
Download or clone the project in your codegniter_project/sparks directory.
You need also the sparks module installed. For more info see [here](http://getsparks.org/install).
Rename the base directory from Paginator-master to Paginator if necessary.
In your codeigniter_project/application/config/autoload.php declare the usage of this spark:
    
    $autoload['sparks'] = ['Paginator/x.x.x'];

# Basic usage
## Controller:
    
    public function my_method($items_per_page = 10, $page = 1, $order_direction = 'desc', $param = null) {
        
        // Paginator functions

        // Set the model to use
        set_model('my_model');
        
        // Set translation file/files
        // This helper can accept array of filenames
        set_translation_file('translation_file', 'language');

        // You can use both - pure string or active record 
        // I preffer to use string, because active record does not work well
        $query = "SELECT * FROM table_name";
        
        $data = get_data($query, 'table_name', $items_per_page, $page, $order_direction, $param);

        $this->load->view('template', ['data' => $data, 'order_direction' => $order_direction, 'items_per_page' => (int)$items_per_page, 'page' => $page, 'param' => $param]);
    }
    
Where ```$items_per_page``` is the count of the items shown per page. 

```$order_direction``` is the direction in which to order the column.
 
```$param``` is the column name which to sort.

```$page``` is the current page.

Use string based queries. You can use also the active record class and his methods, but keep in mind that this method is a little bit buggy and does not give good results. This will be fixed in the future.

## Template helpers: 

For each one helper the uri parameter must be in the following format:
'controller_name/action_name' 

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

Creates a dropdown with items per page values. Those values can be set in the spark's
config file. See ```$config['items_per_page']``` .
The first argument is the uri of the controller - 'my_controller/my_action'
The second argument is the current items per page value.
The third argument is array with attributes for the items per page form, 
e.g. ['class' => 'my_css_class', 'id' => 'my_css_id'].

There is a javascript file that provides the functionality for the dropdown.
You can find it in the assets folder of the spark (Paginator/x.x.x/assets/js/paginator.js). 

========================
    
    pagination($items_per_page, $uri, $page = 1, $order = null, $param = null)

Creates pager links. The pager is Bootstrap 3 ready.

The first argument is the current items per page value.
The second argument got to be this part from the uri that represents the controller 
and the action ('controller_name/action_name'). 
The third argument is the current page number.
The fourth argument represents the order direction - 'asc' or 'desc'.
The fifth argument is the database table column name that is sorted. 
