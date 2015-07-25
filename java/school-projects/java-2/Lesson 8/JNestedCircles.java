/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 25.
 * Chapter 16, Exercise # 5.
 * Description:
 * Create a JFrame that displays 15 nested circles. You may only use one drawOval().
 */
import javax.swing.*;
import java.awt.*;
public class JNestedCircles extends JFrame
{
    public void paint(Graphics g)
    {
        int x = 250;
        int y = 250;
        int w = 10;
        int h = 10;
        int increase = 0;
        super.paint(g);

        // Draw each oval, larger than the previous.
        for (int i = 0; i < 15; ++i)
        {
            g.drawOval(x, y, w, h);
            increase = i + 23;
            w += increase;
            h += increase;
            x -= increase / 2;
            y -= increase / 2;
        }
    }
    public static void main(String[] args)
    {
        // Create and display the frame.
        JNestedCircles frame = new JNestedCircles();
        frame.setTitle("asdf");
        frame.setDefaultCloseOperation(EXIT_ON_CLOSE);
        frame.setSize(500, 500);
        frame.setVisible(true);
    }
}
