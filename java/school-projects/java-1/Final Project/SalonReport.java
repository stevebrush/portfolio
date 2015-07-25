/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 9, Exercise # 7.
 * The SalonReport application displays sorted Services for a salon.
 */
import java.util.*;
import java.text.*;
import javax.swing.*;
public class SalonReport
{
    /**
     * Additional Requirements:
     * ------------------------
     * a) Enhance the program by displaying a menu that asks the user how they want
     *    to sort the services menu. 1) Sort by Service Description, 2) Sort by Price,
     *    3) Sort by Time (Minutes), or 0) to Exit.
     * b) Add a do...while() loop that keeps prompting the user for the next preferred
     *    sort order until the user finally chooses “0” to exit.
     */
    private static ArrayList<Service> services = new ArrayList<Service>();
    private static JOptionPane pane;
    public static void main(String[] args)
    {
        // Create the services objects.
        services.add(new Service("Cut",        8.00F, 15));
        services.add(new Service("Shampoo",    4.00F, 10));
        services.add(new Service("Manicure",  18.00F, 30));
        services.add(new Service("Style",     48.00F, 55));
        services.add(new Service("Permanent", 18.00F, 35));
        services.add(new Service("Trim",       6.00F, 5));

        pane = new JOptionPane();
        boolean quit = false;
        String input = "";
        int choice = 0;

        do
        {
            // Ask the user how they would like the list of services sorted.
            input = "" + JOptionPane.showInputDialog(null, "How to sort?\n1) description  2) price  3) time  0) quit");
            input = input.trim();

            // The user closed the dialog.
            if (input.equals("null"))
            {
                break;
            }
            else
            {
                try
                {
                    // Attempt to convert the input into an integer.
                    choice = Integer.parseInt(input);

                    // Sort the services according to the user's entry.
                    switch (choice)
                    {
                        // Quit.
                        case 0:
                        quit = true;
                        break;

                        // By description.
                        case 1:
                        sortByDescription();
                        displayServices();
                        break;

                        // By price.
                        case 2:
                        sortByPrice();
                        displayServices();
                        break;

                        // By duration in minutes.
                        case 3:
                        sortByDuration();
                        displayServices();
                        break;

                        // Invalid entry.
                        default:
                        pane.showMessageDialog(null, "Invalid entry.\nPlease enter a number between 0-3.");
                        break;
                    }
                }
                catch (Exception e)
                {
                    pane.showMessageDialog(null, "Invalid entry.\nPlease enter a number between 0-3.");
                }
            }
        }
        while (!quit);
    }

    /**
     * Uses a comparator to sort by description.
     */
    private static void sortByDescription()
    {
        Collections.sort(services, Service.getDescriptionComparator());
    }

    /**
     * Uses a comparator to sort by price.
     */
    private static void sortByPrice()
    {
        Collections.sort(services, Service.getPriceComparator());
    }

    /**
     * Uses a comparator to sort by duration in minutes.
     */
    private static void sortByDuration()
    {
        Collections.sort(services, Service.getDurationComparator());
    }

    /**
     * Print the sorted services on the screen.
     */
    private static void displayServices()
    {
        String message = "";
        for (Service service : services)
        {
            message += formatPrice(service.getPrice()) + " - " + service.getDescription() + " (" + service.getDuration() + " minutes)" + "\n";
        }
        pane.showMessageDialog(null, message);
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
