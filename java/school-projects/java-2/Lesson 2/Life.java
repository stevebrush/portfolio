/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 June 27.
 * Chapter 11, Exercise # 9.
 * The Life class manages data regarding life insurance.
 */
import javax.swing.*;
public class Life extends Insurance
{
    public Life()
    {
        super("life");
    }

    /**
     * Getters and Setters.
     */
    public void setCost()
    {
        cost = 36.00;
    }

    /**
     * Display information about life insurance to user.
     */
    public void display()
    {
        JOptionPane pane = new JOptionPane();
        pane.showMessageDialog(null, "Insurance type: " + getType() + "\nCost: $" + getCost());
    }
}
