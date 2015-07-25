/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 30.
 * Chapter 12, Exercise # 2.
 * The TryToParseDouble application converts a string input into a floating point number.
 * This application also handles the exception if the number fails to convert.
 */
import java.util.*;
public class TryToParseDouble
{
    public static void main(String[] args)
    {
        String input;
        double conversion = 0.0;
        boolean quit = false;
        Scanner scanner = new Scanner(System.in);

        while (!quit) {
            try
            {
                // Get a floating point number from the user.
                System.out.print("Enter a floating point number (x.xx) >>");
                input = scanner.nextLine();
                conversion = Double.parseDouble(input);
                quit = true;
            }
            catch (NumberFormatException e)
            {
                System.out.println("The number you entered was not a floating point.");
                conversion = 0.0;
            }
            System.out.println("Your floating point number: " + conversion);
        }
    }
}
