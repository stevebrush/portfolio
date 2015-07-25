/**
 * @author Steve Brush.
 * Lesson 4, Excercise # 6.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 24
 * The Circle class makes it easy to calculate various properties of a circl,
 * such as its diameter and area.
 */
public class Circle
{
    private int radius;
    private int diameter;
    private double area;

    public Circle()
    {
        setRadius(1);
    }

    /**
     * Getters and setters.
     */
    public int getRadius()
    {
        return radius;
    }
    public int getDiameter()
    {
        return diameter;
    }
    public double getArea()
    {
        return area;
    }

    /**
     * Set the radius property, and calculate the diameter and area.
     */
    public void setRadius(int value)
    {
        radius = value;
        calculateDiameter();
        calculateArea();
    }

    /**
     * Calculates the diameter of the circle, based on the radius.
     **/
    public void calculateDiameter()
    {
        diameter = 2 * radius;
    }

    /**
     * Calculates the area of the circle, based on the radius.
     **/
    public void calculateArea()
    {
        area = Math.PI * radius * radius;
    }
}
