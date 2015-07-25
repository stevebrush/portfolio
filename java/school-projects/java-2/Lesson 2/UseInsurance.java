/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 27.
 * Chapter 11, Exercise # 9.
 * The UseInsurance application demonstrates the use of various Insurance objects.
 */
import javax.swing.*;
public class UseInsurance
{
    public static void main(String[] args)
    {
        JOptionPane pane = new JOptionPane();
        String input = "" + pane.showInputDialog(null, "Type of insurance?");
        Insurance insurance = new Life();
        input = input.trim().toLowerCase();

        // Collect user input.
        if (!input.equals("null"))
        {
            switch (input)
            {
                case "life":
                insurance = new Life();
                break;

                case "health":
                insurance = new Health();
                default:
                break;
            }

            // Set the cost of insurance and display to the user.
            insurance.setCost();
            insurance.display();
        }
    }
}
