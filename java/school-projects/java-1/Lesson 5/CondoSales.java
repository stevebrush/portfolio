/**
 * @author Steve Brush.
 * Lesson 5, Excercise # 3.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 26
 * The CondoSales class helps users determine total costs for various condos,
 * based on the view and various options.
 */
import java.util.*;
import java.text.*;
public class CondoSales
{
    private static Scanner scanner;
    private static int viewChoice;
    private static int counterTopChoice;
    private static int price;
    private static String view;

    public static void main(String[] args)
    {
        scanner = new Scanner(System.in);
        // Start the inquiry.
        fetchViewChoice();
    }

    /**
     * Get the view choice and send it to the processing method.
     */
    public static void fetchViewChoice()
    {
        System.out.println("=========================");
        System.out.println("1) Park View,  2) Golf Course View,  3) Lake View");
        System.out.print("Enter your choice: >> ");
        int choice = scanner.nextInt();
        processViewChoice(choice);
    }

    /**
     * Get the granite counter top choice and send it to the processing method.
     */
    public static void fetchCounterTopChoice()
    {
        System.out.println("Would you like to add granite counter tops to the kitchen?");
        System.out.print("Enter: 1) Yes,  2) No >> ");
        int choice = scanner.nextInt();
        processCounterTopChoice(choice);
    }

    /**
     * Handles the view choice and any errors.
     */
    public static void processViewChoice(int choice)
    {
        switch (choice)
        {
            case 1:
                view = "park";
                price = 150000;
                break;
            case 2:
                view = "golf course";
                price = 170000;
                break;
            case 3:
                view = "lake";
                price = 210000;
                break;
            default:
                view = "";
                price = 0;
                break;
        }
        if (price == 0) {
            System.out.println("You entered an incorrect number. Please enter a number between 1-3.");
            fetchViewChoice();
        } else {
            fetchCounterTopChoice();
        }
    }


    /**
     * Handles the counter top choice and any errors.
     */
    public static void processCounterTopChoice(int choice)
    {
        boolean isValidOption = true;
        String optionMessage = "";
        switch (choice)
        {
            case 1:
                price += 4000;
                optionMessage += "(with granite counter tops) ";
                break;
            case 2:
                break;
            default:
                isValidOption = false;
                break;
        }
        if (!isValidOption)
        {
            System.out.println("You entered an incorrect number. Please enter either 1 or 2.");
            fetchCounterTopChoice();
        }
        else
        {
            System.out.println("--> A " + view + " view condo " + optionMessage + "costs $" + NumberFormat.getNumberInstance(Locale.US).format(price) + ".");
            fetchViewChoice();
        }
    }
}
