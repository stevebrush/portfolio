/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 30.
 * Chapter 12, Exercise # 1.
 * The BadSubscriptCaught application returns a first name based on an index provided by the user.
 * It also serves to test the ArrayIndexOutOfBoundsException.
 */
import java.util.*;
public class BadSubscriptCaught
{
    public static void main(String[] args)
    {
        int index;
        String[] names = {
            "James", "Debbie", "Jaci", "Samantha", "Dallas",
            "Lenny", "John", "David", "Chris", "Marsha"
        };
        boolean quit = false;
        Scanner scanner = new Scanner(System.in);

        while (!quit) {
            try
            {
                // Get an integer from the user.
                System.out.print("Enter an integer between zero and 10 >>");
                index = scanner.nextInt();
                System.out.println("Name found: " + names[index]);
                quit = true;
            }
            catch (ArrayIndexOutOfBoundsException e)
            {
                System.out.println("The integer you entered was out of range.");
            }
            catch (InputMismatchException e)
            {
                System.out.println("Please enter a valid integer.");
                scanner.nextLine();
            }
        }
    }
}
