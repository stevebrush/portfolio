/**
 * @author Steve Brush.
 * Lesson 5, Excercise # 5.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 26
 * The Coffee class allows users to select any number of beverages from the menu,
 * and provides a total cost when done.
 */
import java.util.*;
import java.text.*;
public class Coffee
{
    private static Scanner scanner;
    private static float invoice;
    private static String items;

    public static void main(String[] args)
    {
        invoice = 0.0F;
        items = "";
        scanner = new Scanner(System.in);
        showMenu();
    }

    /**
     * Displays the menu of options to the user.
     */
    private static void showMenu()
    {
        System.out.println("========================");
        System.out.println("|  Jivin' Java Coffee  |");
        System.out.println("|----------------------|");
        System.out.println("| (1) Americano  $1.99 |");
        System.out.println("| (2) Espresso   $2.50 |");
        System.out.println("| (3) Latte      $2.15 |");
        System.out.println("========================");
        System.out.println("SUBTOTAL: " + formatPrice(invoice));
        System.out.print("Choose a beverage from the menu above, or enter '0' (zero) to finish. >>");
        fetchChoice();
    }

    /**
     * Receives a menu choice from the user and checks if the order is complete or not.
     */
    private static void fetchChoice()
    {
        int choice = scanner.nextInt();
        boolean isOrderComplete = false;
        switch (choice) {
            case 1:
            invoice += 1.99F;
            items += "Americano\n";
            break;
            case 2:
            invoice += 2.50F;
            items += "Espresso\n";
            break;
            case 3:
            invoice += 2.15F;
            items += "Latte\n";
            break;
            case 0:
            default:
            isOrderComplete = true;
            break;
        }
        if (isOrderComplete)
        {
            System.out.println("Thank you! Your total is: " + formatPrice(invoice) + ".");
            System.out.println("Items: \n" + items);
        }
        else
        {
            showMenu();
        }
    }

    /**
     * Returns a formatted currency string based on a float value.
     */
    private static String formatPrice(float price)
    {
        DecimalFormat decimalFormat = new DecimalFormat();
        decimalFormat.setMaximumFractionDigits(2);
        decimalFormat.setMinimumFractionDigits(2);
        return "$" + decimalFormat.format(price);
    }

}
