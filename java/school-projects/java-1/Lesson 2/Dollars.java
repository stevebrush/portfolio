/**
 * @author Steve Brush.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 22
 * The Dollars class converts a dollar amount (entered by the user) into
 * their respective currency denominations.
 */

import javax.swing.JOptionPane;
public class Dollars
{

    public static void main(String[] args)
    {

        int dollars;
        int remainingDollars;
        int twenties;
        int tens;
        int fives;
        int ones;
        String input = getDollarsEntered();
        String message;

        // Conver the input into an integer.
        dollars = Integer.parseInt(input);
        message = "$" + dollars + " may be converted into: \n";

        // Twenties.
        twenties = dollars / 20;
        remainingDollars = dollars % 20;

        // Tens.
        tens = remainingDollars / 10;
        remainingDollars = remainingDollars % 10;

        // Fives.
        fives = remainingDollars / 5;
        remainingDollars = remainingDollars % 5;

        // Ones.
        ones = remainingDollars;

        // Build the output string.
        // Twenty(ies)
        if (twenties == 1)
        {
            message += "1 twenty\n";
        }
        else
        {
            message += twenties + " twenties\n";
        }

        // Ten(s)
        if (tens == 1)
        {
            message += "1 ten\n";
        }
        else
        {
            message += tens + " tens\n";
        }

        // Five(s)
        if (fives == 1) {
            message += "1 five\n";
        }
        else
        {
            message += fives + " fives\n";
        }

        // One(s)
        if (ones == 1)
        {
            message += "1 one";
        }
        else
        {
            message += ones + " ones";
        }

        // Display the results to the user.
        JOptionPane.showMessageDialog(null,  message);

    }

    /**
     * Returns a string (representing dollars) entered by the user.
     */
    public static String getDollarsEntered()
    {
        String result = JOptionPane.showInputDialog(null, "How many dollars?", "Dollar Denominations", JOptionPane.QUESTION_MESSAGE);
        return result;
    }
}
