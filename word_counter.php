<?php
/**********
 * This script takes a file input and counts the occurance of each word in the file
 */
$hash = array();
if (count($argv) != 2) {
  //error
  echo "Please enter the name of the file to parse";
} else {
  // aggregate the words
  $input_file = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  if ($input_file === false) {
    echo "Error opening the file $argv[2]\n";
    return;
  }
  foreach ($input_file as $line) {
    $words = explode(" ", $line);
    foreach ($words as $word) {
      $word = trim($word, ",.?!:;\t\n\r\0\x0B"); //strip any punctuation from the word
      if (strlen($word) == 0) {
        continue;
      }
      if ($hash[$word]) {
        $hash[$word] += 1;
      } else {
        $hash[$word] = 1;
      }
    }
  }

  // sort by most frequently found
  arsort($hash);

  // print the results
  echo "Word count:\n";
  foreach ($hash as $key => $value) {
    echo "$key: $value\n";
  }
}

?>
