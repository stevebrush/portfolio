/**
 * @author Steve Brush.
 * Lesson 7, Excercise # 1.
 * CIS163AA
 * Class # 21432
 * 2015 May 5
 * The BabyNameComparison class takes three names and displays all possible
 * 2-name combinations.
 */
import java.util.*;
public class BabyNameComparison
{
    public static void main(String[] args)
    {
        Scanner scanner = new Scanner(System.in);
        String[] names = new String[3];
        String firstName = "";

        // Ask the user to provide three (3) names.
        System.out.print("Enter name #1 >>");
        names[0] = scanner.nextLine();
        System.out.print("Enter name #2 >>");
        names[1] = scanner.nextLine();
        System.out.print("Enter name #3 >>");
        names[2] = scanner.nextLine();

        // Print the possible combinations to the user.
        System.out.println("======================");
        System.out.println("Possible Combinations:");
        System.out.println("----------------------");
        for (int i = 0; i < 3; ++i)
        {
            firstName = names[i];
            for (int j = 0; j < 3; ++j)
            {
                // Don't include the same name twice!
                if (!firstName.equals(names[j])) {
                    System.out.println(firstName + " " + names[j]);
                }
            }
        }
        System.out.println("======================");
    }
}
