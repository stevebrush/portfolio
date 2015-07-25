/**
 * @author Steve Brush.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 22
 * The InchesToFeetInteractive class translates an entered value (inches) into
 * both feet and inches and displays it to the user.
 */

import javax.swing.JOptionPane;
public class InchesToFeetInteractive
{

    private static final int INCHES_PER_FEET = 12;

    public static void main (String[] args)
    {

        int inches;
        int feet;
        int remainingInches;
        String message;

        // Collect and formulate data.
        inches = getInchesEntered();
        feet = inches / INCHES_PER_FEET;
        remainingInches = inches % INCHES_PER_FEET;

        // Construct the message.
        if (inches <= 0 || feet == 0)
        {
            message = "Please enter a number greater than " + INCHES_PER_FEET;
        }
        else if (remainingInches == 0)
        {
            message = inches + " inches is equivalent to " + feet + " feet.";
        }
        else
        {
            message = inches + " inches is equivalent to " + feet + " feet and " + remainingInches + " inches.";
        }

        // Print the message to the user.
        JOptionPane.showMessageDialog(null, message);

    }

    /**
     * Returns a number entered by the user.
     */
    public static int getInchesEntered()
    {
        String result;
        int inches;
        result = JOptionPane.showInputDialog(null, "How many inches?", "Inches to Feet Converter", JOptionPane.QUESTION_MESSAGE);
        inches = Integer.parseInt(result);
        return inches;
    }

}
