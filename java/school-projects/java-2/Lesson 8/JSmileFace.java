/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 25.
 * Chapter 16, Exercise # 7.
 * Description:
 * Create a JFrame that displays a yellow smiley face on the screen.
 */
import javax.swing.*;
import java.awt.*;
import java.awt.Color;
public class JSmileFace extends JFrame
{
    public JSmileFace()
    {
        // Create the JFrame.
        super("Smiley");
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(500, 500);
        setVisible(true);
    }
    public void paint(Graphics g)
    {
        // Draw the various components to make up the face.
        super.paint(g);
        g.setColor(Color.YELLOW);
        g.fillOval(50, 50, 400, 400);
        g.setColor(Color.WHITE);
        g.fillOval(120, 130, 50, 50);
        g.fillOval(380, 130, 50, 50);
        g.fillArc(100, 100, 340, 300, 0, -180);
        g.setColor(Color.BLACK);
        g.fillOval(120, 120, 40, 40);
        g.fillOval(380, 120, 40, 40);
        g.fillArc(100, 120, 340, 300, 0, -180);

    }
    public static void main(String[] args)
    {
        JSmileFace frame = new JSmileFace();
    }
}
