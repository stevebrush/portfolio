/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 27.
 * Chapter 11, Exercise # 3.
 * The Auto class manages various information regarding an automobile.
 */
public abstract class Auto
{
    protected String make;
    protected String model;
    protected int price;

    /**
     * Getters and Setters.
     */
    public String getMake()
    {
        return make;
    }
    public void setMake(String value)
    {
        make = value;
    }
    public String getModel()
    {
        return model;
    }
    public void setModel(String value)
    {
        model = value;
    }
    public int getPrice()
    {
        return price;
    }

    /**
     * Sets the price, but not here.
     */
    public abstract void setPrice(int value);
}
