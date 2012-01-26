<?php

class RestaurantFinder {
	public $restaurants = array();
	public $items_to_buy = array();

	function __construct( $args ) {
		$arg_size = count($args);
		if ($arg_size < 3) {
			exit("Error: Invalid number of arguments");
		}

		for ($i = 1; $i < $arg_size; $i++) {
			if ($i == 1) {
				$menu_file = $args[$i];
			} else {
				array_push($this->items_to_buy, $args[$i]);
			}
		}

		$this->parse_menu_file($menu_file);
		$this->find_best_restaurant();
	}

	/* parses a CSV string and returns the CSV as an array
	 * Lines are separated by a carriage-return
	 * Values are separated by a comma
	 */
	function csv_to_array($csv_string) {
		$csv = explode("\n", trim($csv_string));
		foreach( $csv as $key => $value) {
			$csv[$key] = array_map('trim', explode(",", trim($value)));	
		}

		return $csv;
	}

	/* Given an array of items, parse the array and add the items to the menu
	 * each entry in the array has the following embedded array
	 * format: [restaurant_id, price, item1, item2, ..., itemX]
	 * input - array of items to add
	 */
	function add_menu_items ($menu_items) {
		foreach ($menu_items as $item) {
			$restaurant_id = null;
			$price = null;
			$items = array();
			foreach ($item as $key => $value) {
				if ($key == 0) {
					$restaurant_id = intval($value);
				} else if ($key == 1) {
					$price = floatval($value);
				} else {
					array_push($items, $value);
				}
			}
			$this->restaurants[$restaurant_id][implode(",", $items)] = $price; 
		}
	}

	/* Read the input file, parse it to an array, add the array items to the menu
	 * input - path to a CSV file with menu items
	 */
	function parse_menu_file($menu_file) {
		$menu_items = file_get_contents($menu_file);
		if ($menu_items == false) {
			exit("Error: Invalid input file");
		}

		// parse the CSV to an array
		$menu_items = $this->csv_to_array($menu_items);

		$this->add_menu_items($menu_items);
	}

	/* Find the given item in a restaurant menu array
	 * input - string name of item,
	 *         array to search
	 * return - an array of all matching items
	 */
	function find_item_in_menu ($item, $menu_array) {
		$matches = preg_grep("/".$item."/", array_keys($menu_array)); // search the array keys for the item names
		$items = array();
		foreach ($matches as $match) {
			$items[$match] = $menu_array[$match];
		}
		return $items;
	}
	
	/* Finds the restaurant with all the items_to_buy
	 * for the cheapest price and ouputs the restaurant and total price
	 * will output "nil" if no restaurant has all the items specified
	 */
	function find_best_restaurant() {
		$best_restaurant = null;
		$best_price = null;
		foreach ($this->restaurants as $restaurant => $menu) {
			$found_all_items = true;
			$cost = 0;
			foreach ($this->items_to_buy as $item) {
				$matches = $this->find_item_in_menu($item, $menu);
				if (count($matches) == 0) {
					$found_all_items = false;
				} else if (count($matches) == 1) {
					$cost += array_pop($matches);	
				} else {
					// TODO handle multiple items
				}
			}
			if ($found_all_items) {
				if ($best_price === null || $cost < $best_price) {
					$best_price = $cost;
					$best_restaurant = $restaurant;
				}
			}
		}
		if ($best_restaurant === null) {
			echo "nil\n";
		} else {
			echo $best_restaurant . ", " . $best_price . "\n";
		}
	}

}

$restaurant_finder = new RestaurantFinder($argv);

?>
