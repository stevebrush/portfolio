/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 6.
 * The Package class tracks the shipping cost for an item of given weight and shipping method.
 */
public class Package
{
    protected int weight;
    protected char shippingMethod; // A, T, M
    protected float shippingCost;
    public Package(int weight, char shippingMethod)
    {
        this.weight = weight;
        this.shippingMethod = shippingMethod;
        calculateCosts();
    }

    /**
     * Calculates the cost of shipping, based on weight and method.
     */
    private void calculateCosts()
    {
        float rate = 0.0F;

        // Large package.
        if (weight > 16)
        {
            switch (shippingMethod)
            {
                case 'A':
                rate = 4.50F;
                break;
                case 'T':
                rate = 3.25F;
                break;
                case 'M':
                rate = 2.15F;
                break;
            }
        }

        // Medium package.
        else if (weight > 8)
        {
            switch (shippingMethod)
            {
                case 'A':
                rate = 3.00F;
                break;
                case 'T':
                rate = 2.35F;
                break;
                case 'M':
                rate = 1.5F;
                break;
            }
        }

        // Small package.
        else
        {
            switch (shippingMethod)
            {
                case 'A':
                rate = 2.00F;
                break;
                case 'T':
                rate = 1.50F;
                break;
                case 'M':
                rate = 0.50F;
                break;
            }
        }

        shippingCost = rate;
    }

    /**
     * Returns a human-readable version of the shipping method.
     */
    protected String getFormattedShippingMethod()
    {
        String label = "";
        switch (shippingMethod)
        {
            case 'A':
            label = "Air";
            break;
            case 'T':
            label = "Truck";
            break;
            case 'M':
            label = "Mail";
            break;
        }
        return label;
    }

    /**
     * Displays all fields for this package.
     */
    public void display()
    {
        System.out.println("===========================");
        System.out.println("| Package Metrics:");
        System.out.println("===========================");
        System.out.println("| Weight: " + weight + " oz.");
        System.out.println("| Method: " + getFormattedShippingMethod());
        System.out.println("| Cost: $" + shippingCost);
        System.out.println("===========================");
    }
}
