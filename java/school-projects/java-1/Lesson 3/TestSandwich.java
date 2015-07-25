/**
 * @author Steve Brush.
 * Lesson 3, Excercise # 11.
 * CIS163AA
 * Class # 21432
 * 2015 Apr 23
 * The TestSandwich class displays both the defaults of a Sandwich object, as well
 * as the properties of a Sandwich object that have been set explicitly.
 */
import javax.swing.JOptionPane;
public class TestSandwich
{
    public static void main(String[] args)
    {
        Sandwich sandwich = new Sandwich();
        Sandwich standard = new Sandwich();
        String message;

        // Set the properties for the today's 'special'.
        sandwich.setPrimaryIngredient("Beef Brisket");
        sandwich.setBreadType("Sourdough");
        sandwich.setPrice(7.69);

        // Construct the message.
        message = "Welcome to our pantry!\nThe special today is a " + sandwich.getPrimaryIngredient() + " Sandwich on " + sandwich.getBreadType() + " bread, for only $" + sandwich.getPrice() + ".\n";
        message += "If that doesn't sound tasty, you can try our standard " + standard.getPrimaryIngredient() + " Sandwich on " + standard.getBreadType() + " bread, for only $" + standard.getPrice() + ".";

        // Display the message to the user.
        JOptionPane.showMessageDialog(null, message);
    }
}
