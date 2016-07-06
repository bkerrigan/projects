<?php

require_once("gumball_machine.php");

$numOfGumballs =10;
$numOfColors = 7;
$costPerTurn = 0.75; // 75 cents.
$gm = new GumballMachine($numOfGumballs, $numOfColors, $costPerTurn);

$payment = 0.5;  // 50 cents.
$result = $gm->TurnKnob($payment);
print("Result for payment of " . $payment . " is: " . $result . ".\n");
// Result for payment of 0.5 is: Insufficient payment.

$payment = 0.75;
for ($i = 0; $i <= $numOfGumballs; $i++) {
	$result = $gm->turnKnob($payment);
	echo "Loop " . $i . ".  Result for payment of " . $payment . " is: " . $result . ".\n";
}

?>
