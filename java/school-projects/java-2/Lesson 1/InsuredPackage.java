/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 6.
 * The InsuredPackage class extends the Package class and adds insurance calculations.
 */
public class InsuredPackage extends Package
{
    private float insuranceCost;
    public InsuredPackage(int weight, char shippingMethod)
    {
        super(weight, shippingMethod);
        calculateInsuranceCosts();
    }

    /**
     * Calculates the insurance cost based on the initial shipping cost.
     */
    private void calculateInsuranceCosts()
    {
        // Premium shipping cost
        if (shippingCost > 3.00F)
        {
            insuranceCost = 5.55F;
        }

        // Standard shipping cost
        else if (shippingCost > 1.00F)
        {
            insuranceCost = 3.95F;
        }

        // Budget shipping cost
        else
        {
            insuranceCost = 2.45F;
        }
    }

    /**
     * Displays all fields related to this package.
     */
    public void display()
    {
        System.out.println("===========================");
        System.out.println("| Package Metrics:");
        System.out.println("===========================");
        System.out.println("| Weight: " + weight + " oz.");
        System.out.println("| Method: " + getFormattedShippingMethod());
        System.out.println("| Cost: $" + shippingCost);
        System.out.println("| Insurance Cost: $" + insuranceCost);
        System.out.println("===========================");
    }
}
