/**
 * @author Steve Brush.
 * Lesson 4, Excercise # 6.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 24
 * The TestCircle class tests the Circle class to make sure its methods work appropriately.
 */
public class TestCircle
{
    public static void main(String[] args)
    {
        Circle circle1 = new Circle();
        Circle circle2 = new Circle();
        Circle circle3 = new Circle();

        // Set the radius' for two of the cirlce objects.
        circle1.setRadius(5);
        circle2.setRadius(9583);

        // Print the results to the user.
        System.out.println("Circle # 1's radius is " + circle1.getRadius() + "; it's diameter is " + circle1.getDiameter() + "; and it's area is " + circle1.getArea() + ".");
        System.out.println("Circle # 2's radius is " + circle2.getRadius() + "; it's diameter is " + circle2.getDiameter() + "; and it's area is " + circle2.getArea() + ".");
        System.out.println("Circle # 3's radius is " + circle3.getRadius() + "; it's diameter is " + circle3.getDiameter() + "; and it's area is " + circle3.getArea() + ".");
    }
}
