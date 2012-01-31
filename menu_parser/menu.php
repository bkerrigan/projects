<?php

class MenuItem {
	public $item = array();
	public $price;

	function __construct($items, $price) {
		$this->item = $items;
		if (count($items) > 1) {
			$isCombo = true;
		}
		$this->price = $price;
	}
}

class RestaurantFinder {
	public $restaurants = array();
	public $items_to_buy = array();

	function __construct( $args ) {
		$arg_size = count($args);
		if ($arg_size < 3) {
			exit("Error: Invalid number of arguments\n");
		}

		for ($i = 1; $i < $arg_size; $i++) {
			if ($i == 1) {
				$menu_file = $args[$i];
			} else {
				array_push($this->items_to_buy, $args[$i]);
			}
		}

		$this->parse_menu_file($menu_file);
		$this->sort_menus();
		$this->find_best_restaurant();
	}

	/* Sort the manu at each restaurant so the combo items
	 * are the first items in the menu
	 */
	 function sort_menus() {
		$compare = function($a, $b) {
			// sort in descending order by number of items
			$acount = count($a->item);
			$bcount = count($b->item);
			if ($acount == $bcount) {
				return 0;
			}
			return ($acount > $bcount) ? -1 : 1;
		};
		foreach ($this->restaurants as $key => $restaurant) {
			usort($restaurant, $compare);
			$this->restaurants[$key] = $restaurant;
		}
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
			$mitem = new MenuItem($items, $price);
			if (!array_key_exists($restaurant_id, $this->restaurants)) {
				$this->restaurants[$restaurant_id] = array();
			}
			array_push($this->restaurants[$restaurant_id], $mitem); 
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
		$matches = array();
		foreach ($menu_array as $mitem) {
			foreach ($mitem->item as $citem) {
				if ($item === $citem) {
					array_push($matches, $mitem);
					break;
				}
			}
		}
		return $matches;
	}

	/* Find the given item in a restaurant menu array
	 * input - string name of item,
	 *         array to search
	 * return - price of the cheapest matching item
	 *          null if there is no matching item
	 */
	function find_cheapest_match_in_menu($item, $menu) {
		$matches = $this->find_item_in_menu($item, $menu);
		if (count($matches) == 0) {
			return null;
		}
		// find the cheapest option for that item
		$cheapest = null;
		foreach ($matches as $match) {
			if ($cheapest === null || $match->price < $cheapest) {
				$cheapest = $match->price;
			}
		}

		return $cheapest;
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
			$shopping_list = $this->items_to_buy;
			foreach ($shopping_list as $item) {
				$matches = $this->find_item_in_menu($item, $menu);
				if (count($matches) == 0) {
					$found_all_items = false;
					break;
				} else {
					// find the cheapest option for that item
					$cheapest_item = null;
					foreach ($matches as $matching_item) {
						$price = $matching_item->price;
						if (count($matching_item->item) > 1) {
							// combo deal, check for other items
							$combo_value = 0;
							foreach ($matching_item->item as $citem) {
								if (in_array($citem, $shopping_list)) {
									// look up the item price, add to $combo_value
									$cprice = $this->find_cheapest_match_in_menu($citem, $menu);
									if ($cprice === null) continue;
									$combo_value += $cprice;
								}
							}
							if ($combo_value > $price) {
								// use the combo item
								$cost += $price;
							} else {
								// use the individual items
								$cost += $combo_value;
							}
							// we already added the items so remove them from the items_to_buy list
							foreach ($matching_item->item as $combo_item) {
								$key = array_search($combo_item, $shopping_list);
								if ($key === false) {
									continue;
								}
								unset($shopping_list[$key]);
							}
							// if we've found a combo item that matches then 
							// we've checked all possible matches, break out of the loop
							break;
						} else {
							// not a combo item
							if ($cheapest_item === null || $price < $cheapest_item) {
								$cheapest_item = $price;
								echo "Adding cheapest item: " . $price . " to the cost\n";
							}
						}
					}
					$cost += $cheapest_item;
				}
			}
			if ($found_all_items) {
				echo "Found all items at restaurant " . $restaurant . " cost is " . $cost . "\n";
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
