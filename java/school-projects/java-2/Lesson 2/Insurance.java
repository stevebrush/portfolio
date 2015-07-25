/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 27.
 * Chapter 11, Exercise # 9.
 * The Insurance class provides methods and properties for various types of insurances.
 */
public abstract class Insurance
{
    protected String type;
    protected double cost;
    public Insurance(String type)
    {
        this.type = type;
    }

    /**
     * Getters and Setters.
     */
    public String getType()
    {
        return type;
    }
    public double getCost()
    {
        return cost;
    }

    /**
     * Abstract methods.
     */
    public abstract void setCost();
    public abstract void display();
}
