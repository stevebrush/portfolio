/**
 * @author Steve Brush.
 * Lesson 3, Excercise # 10.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 23
 * The Interest class calculates the total number of dollars earned based on a
 * static interest rate and a period of years.
 */
import javax.swing.JOptionPane;
import java.text.DecimalFormat;
public class Interest
{

    private static final float INTEREST_PERCENTAGE = 0.05F;

    public static void main(String[] args)
    {
        String result = getDollarsEntered();
        int investment = Integer.parseInt(result);
        float total = calculateTotalInterest(investment, 1);
        JOptionPane.showMessageDialog(null, "Over a period of one year, your investment would be " + formatPrice(total) + ".");
    }

    /**
     * Returns the input string from a dialog box, representing dollars.
     */
    private static String getDollarsEntered()
    {
        return JOptionPane.showInputDialog(null, "How much money would you like to invest?");
    }

    /**
     * Returns a float representing the total dollars earned with interest
     * over a period of years.
     */
    private static float calculateTotalInterest(int dollars, int years)
    {
        return dollars + (INTEREST_PERCENTAGE * dollars * years);
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
