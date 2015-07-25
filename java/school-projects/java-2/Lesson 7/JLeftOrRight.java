/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 14.
 * Chapter 15, Exercise # 7.
 * Description:
 * Write an application that lets you determine the integer value returned by
 * the InputEvent method getModifiers() when you click your left, right, or
 * middle mouse button on a JFrame.
 */
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
public class JLeftOrRight extends JFrame implements MouseListener
{
    private JLabel label = new JLabel("Click the screen...");

    /**
     * Constructor.
     */
    public JLeftOrRight()
    {
        super("Which Mouse Button?");
        setSize(500, 500);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        addMouseListener(this);
        add(label);
        setVisible(true);
    }

    /**
     * Display the mouse click modifier to the user when the mouse is clicked.
     */
    @Override
    public void mouseClicked(MouseEvent e) {
        int modifier = e.getModifiers();
        if (modifier == MouseEvent.BUTTON1_MASK)
        {
            label.setText("Left button clicked (" + modifier + ")");
        }
        else if (modifier == MouseEvent.BUTTON2_MASK)
        {
            label.setText("Middle button clicked (" + modifier + ")");
        }
        if (modifier == MouseEvent.BUTTON3_MASK)
        {
            label.setText("Right button clicked (" + modifier + ")");
        }
    }
    @Override
    public void mouseEntered(MouseEvent e) {}

    @Override
    public void mouseExited(MouseEvent e) {}

    @Override
    public void mousePressed(MouseEvent e) {}

    @Override
    public void mouseReleased(MouseEvent e) {}

    /**
     * Init.
     */
    public static void main(String[] args)
    {
        JLeftOrRight frame = new JLeftOrRight();
    }
}
