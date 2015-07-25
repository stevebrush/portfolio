/**
 * @author Steve Brush.
 * Lesson 6, Excercise # 14.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 27
 * The CreatePurchase class collects an invoice number and sale amount and returns
 * the total sale amount.
 */
import java.util.*;
public class CreatePurchase
{
    public static void main(String[] args)
    {
        Purchase myPurchase = new Purchase();
        Scanner scanner = new Scanner(System.in);
        int invoiceNumber;
        float saleAmount;
        boolean hasError = false;

        // Capture the invoice number.
        do
        {
            if (hasError) {
                System.out.println("Invalid entry. Try again.");
            }
            System.out.print("Enter invoice number: >>");
            invoiceNumber = scanner.nextInt();
            hasError = true;
        }
        while (invoiceNumber < 1000 || invoiceNumber > 8000);

        // Capture the sale amount.
        hasError = false;
        do
        {
            saleAmount = 0.0F;

            // The sale amount entered is less than zero.
            if (hasError) {
                System.out.println("Please enter a valid number greater than or equal to zero.");
            }

            System.out.print("Enter sale amount: >>$");

            // Only allow float.
            while (!scanner.hasNextFloat())
            {
                scanner.next();
                System.out.print("Incorrect value. Please try again >>");
            }

            saleAmount = scanner.nextFloat();
            hasError = true;
        }
        while (saleAmount <= 0.0F);

        myPurchase.setSaleAmount(saleAmount);

        // Print the results to the user.
        System.out.println("=============================");
        System.out.println("Invoice # " + invoiceNumber);
        System.out.println("=============================");
        myPurchase.printTotals();
    }
}
