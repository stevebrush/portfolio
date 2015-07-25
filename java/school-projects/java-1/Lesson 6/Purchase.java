/**
 * @author Steve Brush.
 * Lesson 6, Excercise # 14.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 27
 * The Purchase class handles the total sale amount of a purchase, including sales tax.
 */
import java.text.*;
public class Purchase
{
    private int invoiceNumber;
    private float saleAmount;
    private float salesTaxAmount;
    private float totalSaleAmount;
    private final float SALES_TAX_PERCENTAGE = 0.05F;

    /**
     * Sets invoiceNumber.
     */
    public void setInvoiceNumber(int value)
    {
        invoiceNumber = value;
    }

    /**
     * Sets saleAmount, salesTaxAmount, and totalSaleAmount.
     */
    public void setSaleAmount(float value)
    {
        saleAmount = value;
        salesTaxAmount = saleAmount * SALES_TAX_PERCENTAGE;
        totalSaleAmount = saleAmount + salesTaxAmount;
    }

    /**
     * Displays the totals to the user.
     */
    public void printTotals()
    {
        System.out.println("Subtotal: " + formatPrice(saleAmount));
        System.out.println("Taxes:    " + formatPrice(salesTaxAmount));
        System.out.println("Total:    " + formatPrice(totalSaleAmount));
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
