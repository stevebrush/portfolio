/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 4, Game Zone # 1 & 2.
 * The FiveDice application creates a number of Die objects and displays their
 * random values to the player.
 */
import java.util.*;
public class FiveDice
{
    /**
     * Additional Requirements:
     * ------------------------
     * 1) Enhance the FiveDice class so that it stores the randomly “thrown” dice
     *    in an array of Die objects. Display the results for both the player and
     *    computer for each round.
     */

    private static final int NUM_DIE_PER_PLAYER = 5;
    private static Die[] computerDice = new Die[NUM_DIE_PER_PLAYER];
    private static Die[] playerDice = new Die[NUM_DIE_PER_PLAYER];

    public static void main(String[] args)
    {
        createDice();

        String computerDiceMessage = "";
        String playerDiceMessage = "";

        // Create the messages that will show the values of each die.
        for (int i = 0; i < NUM_DIE_PER_PLAYER; ++i)
        {
            computerDiceMessage += computerDice[i].getValue() + " | ";
            playerDiceMessage += playerDice[i].getValue() + " | ";
        }

        // Display the values of the dice to the player.
        System.out.println("Computer Dice:\n" + computerDiceMessage + "\n\nPlayer Dice:\n" + playerDiceMessage);
    }

    /**
     * Creates Die objects for the computer and the player.
     **/
    private static void createDice()
    {
        for (int i = 0; i < NUM_DIE_PER_PLAYER; ++i)
        {
            computerDice[i] = new Die();
            playerDice[i] = new Die();
        }
    }
}
