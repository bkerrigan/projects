<?php
Class DocSearch {
	public $file;
	public $words = array();

	public function __construct($args) {
		$arg_size = count($args);
		for ($i = 1; $i < $arg_size; $i++) {
			if ($i == 1) {
				$this->file = $args[$i];
			} else {
				array_push($this->words, $args[$i]);
			}
		}

		if (!is_readable($this->file)) {
			exit("Error: File not found\n");
		}
		$this->do_search();
	}

	/* 
	 * Opens the given file, searhces for the specified key words
	 */
	public function do_search() {
		$snippet = array();
		$add_to_snippet = false;

		$matches = array();
		$first_match;

		$shortest = 0;
		$shortest_snip = "";
		$word_count = 0;

		// convert the search words to lower case
		$search_words = array();
		foreach ($this->words as $item) {
			array_push($search_words, strtolower($item));
		}
		$search_words = array_unique($search_words);	// make sure there are no duplicate search words
		
		// start processing the file
		$file = @fopen($this->file, "r");
		if ($file == false) {
			exit("Error: Unable to open file $this->file\n");
		}

		while (($buf = fgets($file)) !== false) {
			$buf = explode(" ", $buf);
			foreach ($buf as $w) {
				$w_trimmed = strtolower(trim($w, ",.;:/\\ \n\t\r")); 
				if ($add_to_snippet) {
					array_push($snippet, $w);	// don't add to the snippet until we've found the first search word
				}

				if (in_array($w_trimmed, $search_words)) {
					$matches[$word_count] = $w_trimmed;	// add the word to the list of matching words with its position

					// if this is the first match, start recording the text snippet
					if (!$add_to_snippet) {
						$add_to_snippet = true;
						array_push($snippet, $w);
					}
					
					/* process the array of matching words
					 * number of matches is >= the number of words searched for if:
					 * Case 1: The first matching word is repeated in the list
					 * Case 2: All search words are found and we have a valid snippet
					 * Case 3: Not all search words are found, at least one aside from the first is repeated
					 */
					while (count($matches) >= count($search_words)) {
						$first_word = null;
						$first_position = 0;
						$word_check = array();

						// generate a unique list of the matching words
						foreach ($matches as $key => $value) {
							if ($first_word === null) {
								$first_word = $value;
								$first_position = $key;
								$word_check[$value] = 1;
								continue;
							}
							if (array_key_exists($value, $word_check)) {
								$word_check[$value] += 1;
							} else {
								$word_check[$value] = 1;
							}
						}

						if ($word_check[$first_word] > 1) {
							// Case 1: the first word in the matches occurs twice
							// shift the first match out of the array
							unset($matches[$first_position]);
						} else if (count($word_check) >= count($search_words)) {
							// Case 2: we've found a valid snippet
							// check its length versus the best so far
							$last_position = key(array_slice($matches, -1, 1, TRUE));
							if ($shortest == 0 || 
								$last_position - $first_position < $shortest) {
								$shortest = $last_position - $first_position;
								$shortest_snip = implode($snippet, " ");
							}
							unset($matches[$first_position]);
						} else {
							// Case 3: not all the search words have been found
							// a word other than the first word was found twice
							// continue searching until we find all the search words
							break;
						}
						// The first word in matches has been removed
						// need to shift the words in the snippet out until the next match is reached
						$next_match = array_shift(array_values($matches));
						array_shift($snippet);	//shift out the previous matching search word from the snippet
						while (count($snippet) > 0 && 
							trim(array_shift(array_values($snippet)), ",.;:/\\ \n\t\r") != $next_match) {
							array_shift($snippet);
						}
					}
				}
				$word_count++;
			}
		}
		fclose($file);
		if ($shortest > 0) {
			echo "The shortest snippet ($shortest) is:\n $shortest_snip\n";
		} else {
			echo "No match found for search words $this->words\n";
		}
	}

}

$docs = new DocSearch($argv);
?>
