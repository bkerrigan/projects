<?php

class GumballMachine {
	private $num_gumballs = 0;
	private $cost_per_gumball = 0;
	private $colors = array('Red', 'Blue', 'Green', 'Yellow', 'Orange', 'Purple');
	private $num_colors;

	/* Initialize the gumball machine variables
	 * $num_gumballs must be greater than or equal to 0
	 * $num_colors must be greater than 0 and less than the number
	 * of colors in the $colors array
	 * $cost must be greater than or equal to zero
	 */
	function __construct( $num_gumballs, $num_colors, $cost ) {
		if ( $num_gumballs >= 0 ) {
			$this->num_gumballs = $num_gumballs;
		}

		if ( $cost >= 0 ) {
			$this->cost_per_gumball = $cost;
		}

		$this->num_colors = count($this->colors);
		if ( $num_colors > 0 && $num_colors < $this->num_colors ) {
			$this->num_colors = $num_colors;
		}
	}

	/* Gets a gumball from the machine if there is sufficient payment
	 * returns the result from the gumball machine
	 */
	function TurnKnob( $payment ) {
		if ( $payment < $this->cost_per_gumball ) {
			return "Insufficient payment";
		}

		if ( $this->num_gumballs <= 0 ) {
			return "Out of gumballs";
		}

		$this->num_gumballs--;
		$color = $this->colors[rand(0,$this->num_colors - 1)];
		return $color . " gumball";
	}
}

?>
