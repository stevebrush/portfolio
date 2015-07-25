/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 27.
 * Chapter 11, Exercise # 9.
 * The Health class manages data regarding health insurance.
 */
import javax.swing.*;
public class Health extends Insurance
{
    public Health()
    {
        super("health");
    }

    /**
     * Getters and Setters.
     */
    public void setCost()
    {
        cost = 196.00;
    }

    /**
     * Display information regarding health insurance.
     */
    public void display()
    {
        JOptionPane pane = new JOptionPane();
        pane.showMessageDialog(null, "Insurance type: " + getType() + "\nCost: $" + getCost());
    }
}
