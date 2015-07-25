/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 18.
 * Chapter 10, Exercise # 1.
 * The Horse class stores various data for a standard horse.
 */
public class Horse
{
    private String name;
    private String color;
    private int birthYear;

    /**
     * Getters and Setters.
     */
    public String getName()
    {
        return name;
    }
    public String getColor()
    {
        return color;
    }
    public int getBirthYear()
    {
        return birthYear;
    }
    public void setName(String val)
    {
        name = val;
    }
    public void setColor(String val)
    {
        color = val;
    }
    public void setBirthYear(int val)
    {
        birthYear = val;
    }
}
