/**
 * @author Steve Brush.
 * Lesson 3, Excercise # 5.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 23
 * The GasPrices class returns a range of dollars representing the cost of gasoline
 * per gallon, when the cost of a barrel is entered.
 */
import java.util.Scanner;
import java.text.DecimalFormat;
public class GasPrices
{
    // Reference the method getPricePerGallon to see how these constants were determined.
    private static final float RANGE_MIN = 0.035F;
    private static final float RANGE_MAX = 0.04F;
    public static void main(String[] args)
    {
        int pricePerBarrel;
        float[] pricesPerGallon;
        Scanner input = new Scanner(System.in);

        // Request the cost per barrel from the user.
        System.out.print("What is the current cost per barrel of crude oil (USD)? ");
        pricePerBarrel = input.nextInt();
        input.nextLine(); // Consume the return key, just in case.

        // Get the range of prices per gallon.
        pricesPerGallon = getPricePerGallon(pricePerBarrel);

        // Display the results to the user.
        System.out.println("The approximate cost of gasoline per gallon is between: " + formatPrice(pricesPerGallon[0]) + " - " + formatPrice(pricesPerGallon[1]));
    }

    /**
     * Returns a float array containing the min and max cost per gallon of gasoline.
     */
    public static float[] getPricePerGallon(int pricePerBarrel)
    {
        /**
         * The cost of gas per gallon represents only 60-68% of the cost of crude oil.
         * For example:
         * costPerBarrel = $100;
         * gallonsPerBarrel = 42;
         * costPerBarrel / gallonsPerBarrel = $2.38 per gallon (for crude only)
         * Factor in other costs/taxes:
         * pricePerGallon (min) = $2.38 / .68%
         * pricePerGallon (max) = $2.38 / .60%
         * Summary: pricePerGallon = pricePerBarrel / 42ga / .68%
         * Or, simply: pricePerBarrel * 3.5%
         */
        float[] range = new float[2];
        range[0] = pricePerBarrel * RANGE_MIN;
        range[1] = pricePerBarrel * RANGE_MAX;
        return range;
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
