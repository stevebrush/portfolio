/**
 * @author Steve Brush.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 22
 * The DogBoarding class displays the total cost for boarding, based on the
 * animal's weight, and how many days to be boarded.
 */

import java.util.Scanner;
import java.text.DecimalFormat;
public class DogBoarding
{

    private static final float COST_PER_POUND = 0.5F;
    private static final String ORG_NAME = "Happy Yappy Kennel";

    public static void main(String[] args)
    {

        int daysBoarded;
        int weight;
        float cost;
        String name;
        String formattedCost;
        Scanner input = new Scanner(System.in);

        // Get the dog's name.
        System.out.print("What's your dog's name? ");
        name = input.nextLine();

        // Get the dog's weight.
        System.out.print("How much does " + name + " weigh (lbs.)? ");
        weight = input.nextInt();
        input.nextLine();

        // Get the length of stay.
        System.out.print("How many days will " + name + " stay at " + ORG_NAME + "? ");
        daysBoarded = input.nextInt();

        // Calculate and format the cost.
        cost = calculateTotal(daysBoarded, weight);
        formattedCost = formatPrice(cost);

        // Display the results to the user.
        System.out.println(name + "'s stay (for " + daysBoarded + " days) will cost " + formattedCost + ".");
        System.out.println("Please call 555-485-DOGS to make a reservation!");

    }

    /**
     * Return a float representing the total cost of the stay.
     */
    private static float calculateTotal(int days, int weight)
    {
        float cost;
        cost = COST_PER_POUND * weight * days;
        return cost;
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
