/**
 * @author Steve Brush.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 22
 * The InchesToFeet class translates a literal value (inches) into both feet and
 * inches and displays it to the user.
 */

import javax.swing.JOptionPane;
public class InchesToFeet
{

    private static final int INCHES_PER_FEET = 12;

    public static void main (String[] args)
    {

        int inches = 75;
        int feet = inches / INCHES_PER_FEET;
        int remainingInches = inches % INCHES_PER_FEET;
        String message;

        // Construct the output message.
        if (remainingInches == 0)
        {
            message = inches + " inches is equivalent to " + feet + " feet.";
        }
        else
        {
            message = inches + " inches is equivalent to " + feet + " feet and " + remainingInches + " inches.";
        }

        // Display the results to the user.
        JOptionPane.showMessageDialog(null, message);

    }

}
