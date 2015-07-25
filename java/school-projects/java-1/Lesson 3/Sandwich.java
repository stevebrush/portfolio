/**
 * @author Steve Brush.
 * Lesson 3, Excercise # 11.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 23
 * The Sandwich class represents all sandwiches on the pantry's menu, and allows
 * specific properties to be changed for each sandwich created.
 */
public class Sandwich
{

    private String primaryIngredient;
    private String breadType;
    private double price;

    /**
     * Constructor.
     */
    public Sandwich()
    {
        // Set defaults.
        primaryIngredient = "Turkey";
        breadType = "Rye";
        price = 5.99;
    }

    /**
     * Getters and setters.
     */
    public String getPrimaryIngredient()
    {
        return primaryIngredient;
    }

    public void setPrimaryIngredient(String value)
    {
        primaryIngredient = value;
    }

    public String getBreadType()
    {
        return breadType;
    }

    public void setBreadType(String value)
    {
        breadType = value;
    }

    public double getPrice()
    {
        return price;
    }

    public void setPrice(double value)
    {
        price = value;
    }

    /**
     * End getters and setters.
     */

}
