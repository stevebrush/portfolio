/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 6, Game Zone # 2.
 * The RandomGuess3 application lets a player guess a randomly generated number.
 */
import java.util.*;
public class RandomGuess3
{
    /**
     * Additional Requirements:
     * ------------------------
     * 1) Enhance the game so the player has to pick a number between 1 and 1,000.
     * 2) Once the player has figured out the correct number, ask if they would
     *    like to play again using the Scanner class. If yes, restart the game with
     *    a new random number. If not, use the System.out.println(); method to
     *    display a message that thanks the user for playing the game.
     */
    private static final int MAX = 1000;
    public static void main(String[] args)
    {
        Scanner scanner = new Scanner(System.in);
        int rand = getRandomNumber();
        int numGuesses = 0;
        int guess = 0;
        boolean quit = false;

        // Show the random number for testing purposes.
        // System.out.println("(Shhh! The random number is: " + rand + ")");

        while (!quit)
        {
            // Ask the user to guess the random number.
            System.out.print("Try to guess the number (between 1-1000) >>");
            guess = scanner.nextInt();
            ++numGuesses;

            // The guess is beyond the maximum allowed.
            if (guess > MAX)
            {
                System.out.println("Your guess was beyond the maximum range.");
            }

            // The guess is less than zero; not allowed.
            else if (guess < 0)
            {
                System.out.println("Please guess a number greater than zero.");
            }

            // Tell the user their guess is less than the answer.
            else if (guess < rand)
            {
                System.out.println("Too low.");
            }

            // Tell the user their guess is more than the answer.
            else if (guess > rand)
            {
                System.out.println("Too high.");
            }

            // Success! The user guessed the answer.
            else if (guess == rand)
            {
                System.out.println("**************************************");
                System.out.println("Success! The random number is " + rand + "!\nIt took you " + numGuesses + " guesses.");
                System.out.println("--------------------------------------");
                System.out.print("Would you like to play again? 1) Yes  2) No >>");
                int choice = scanner.nextInt();

                // Play again?
                if (choice == 1)
                {
                    numGuesses = 0;
                    rand = getRandomNumber();
                    System.out.println("**************************************");
                    System.out.println("(Shhh! The random number is: " + rand + ")");
                }

                // Quit the game. Tell the user "Thank you".
                else
                {
                    quit = true;
                    System.out.println("*******************");
                    System.out.println("THANKS FOR PLAYING!");
                    System.out.println("*******************");
                }
            }
        }
    }

    /**
     * Returns a random number within a range.
     */
    private static int getRandomNumber()
    {
        return (1 + (int)(Math.random() * MAX));
    }
}
