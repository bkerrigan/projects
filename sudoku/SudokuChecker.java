import java.io.BufferedReader;
import java.io.FileReader;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.lang.Math;
import java.util.*;

class SudokuChecker {
	int size = 0;	// height and width of the game board
	int[][] board;
	String err_loc = null;

	/* Prints out the location of the first error 
	 * in the solution if one exists
	 */
	void showError() {
		if (err_loc != null) {
			System.out.println(err_loc);
		}
	}

	/* Create the sudoku board and validate the input size
	 * returns true on success, false on failure
	 */
	boolean createBoard (String file) {
		BufferedReader reader = null;

		try {
			reader = new BufferedReader(new FileReader(file));
			String row = null;
			int num_rows = 0;

			while ((row = reader.readLine()) != null) {
				String[] numbers = row.split(",");
				if (size == 0) {
					size = numbers.length;
					// make sure the board size if a perfect square
					int root = (int) Math.floor(Math.sqrt((double) size));
					if (root*root != size) {
						System.err.println("Invalid board size: " + size + " Size must be a perfect square number.");
						return false;
					}
					board = new int[size][size];
				} 
				
				if (size != numbers.length || num_rows >= size) {	// make sure the board is a square
					System.err.println("Invalid board size, rows: " + num_rows + " columns: " + numbers.length);
					return false;
				}

				// add the numbers to the game board
				for (int i = 0; i < size; i++) {
					board[num_rows][i] = Integer.parseInt(numbers[i]);
				}

				num_rows++;
			}
		} catch (FileNotFoundException e) {
			System.err.println("Error: input file not found.");
			e.printStackTrace();
			return false;
		} catch (IOException e) {
			System.err.println("Error processing input file:");
			e.printStackTrace();
			return false;
		} catch (NumberFormatException e) {
			System.err.println("Error: invalid value in input file.");
			e.printStackTrace();
			return false;
		} finally {
			try {
				if (reader != null) {
					reader.close();
				}
			} catch (IOException e) {
				System.err.println("Error processing input file:");
				e.printStackTrace();
				return false;
			}
		}
		return true;
	}

	/* Validates sudoku solution
	 * returns true on success, false on failure
	 */
	boolean validate() {
		// from the array, for each row, column, and region
		// validate that the numbers 1,2,...,size are present
		// add to a set (ensure unique) and compare set size to size
		// sum the values of the numbers, should be n*n+1/2
		int sum = (size * (size + 1))/2;	// the sum of numbers 1 + 2 + ... + size
		int min = 1;
		int grid_size = (int) Math.sqrt((double) size);
		
		// check rows and columns
		for (int i=0; i < size; i++) {
			Set<Integer> row_numbers = new HashSet<Integer>();
			int row_sum = 0;

			Set<Integer> col_numbers = new HashSet<Integer>();
			int col_sum = 0;

			for (int j=0; j < size; j++) {
				if (board[i][j] > size || board[i][j] < min) {
					err_loc = "Number not in allowed range at row: " + i + " column: " + j;
					return false;
				}
				if (board[j][i] > size || board[j][i] < min) {
					err_loc = "Number not in allowed range at row: " + j + " column: " + i;
					return false;
				}
				row_numbers.add(board[i][j]);
				row_sum += board[i][j];

				col_numbers.add(board[j][i]);
				col_sum += board[j][i];
			}

			if (row_sum != sum || row_numbers.size() != size) {
				err_loc = "Incorrect value in row: " + i;
				return false;
			}

			if (col_sum != sum || col_numbers.size() != size) {
				err_loc = "Incorrect value in column: " + i;
				return false;
			}
		}

		// check each of the regions
		for (int i=0; i < size; i+=grid_size) {
			for (int j=0; j < size; j+=grid_size) {
				Set<Integer> grid_numbers = new HashSet<Integer>();
				int grid_sum = 0;
				for (int m=0; m < grid_size; m++) {
					for (int n=0; n < grid_size; n++) {
						if (board[i+m][j+n] < min || board[i+m][j+n] > size) {
							err_loc = "Number not in allowed range at row: " + (i+m) + " column: " + (j+n);
							return false;
						}
						grid_numbers.add(board[i+m][j+n]);
						grid_sum += board[i+m][j+n];
					}
				}
				if (grid_sum != sum || grid_numbers.size() != size) {
					err_loc = "Incorrect value in grid: (" + i + "," + j + ") to (" + (i + grid_size - 1) + "," + (j + grid_size -1) + ")";
					return false;
				}
			}
		}

		return true;
	}

	/* Expects one argument:
	 * a string containing the location of the input file
	 */
	public static void main(String[] argv) {
		if (argv.length != 1) {
			System.err.println("Invalid number of arguments. Usage: java SudokuChecker <path to file>");
			System.exit(1);
		}

		SudokuChecker game = new SudokuChecker();
		if (!game.createBoard(argv[0])) {
			System.exit(1);
		}

		if (game.validate()) {
			System.out.println("Correct solution!");
		} else {
			System.out.println("Incorrect solution!");
			game.showError();
		}
		
	}
}
