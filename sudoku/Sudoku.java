import java.io.BufferedReader;
import java.io.FileReader;
import java.io.FileNotFoundException;
import java.io.IOException;

class Sudoku {
	int size = 0;	// height and width of the game board
	int[][] board;

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
				num_rows++;
				String[] numbers = row.split(",");
				if (size == 0) {
					size = numbers.length;
					board = new int[size][size];
				} else if (size != numbers.length || num_rows >= size) {	// make sure the board is a square
					System.err.println("Invalid board size");
				}

				// add the numbers to the game board
				for (int i = 0; i < numbers.length; i++) {
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
		return true;
	}
}

class Main {

	public static void main(String[] argv) {
		if (argv.length != 1) {
			System.err.println("Invalid number of arguments. Usage: javac Sudoku <path to file>");
			System.exit(1);
		}

		Sudoku game = new Sudoku();
		if (!game.createBoard(argv[0])) {
			System.exit(1);
		}

		if (game.validate()) {
			System.out.println("Correct solution!");
		} else {
			System.out.println("Incorrect solution!");
		}
		
	}
}
