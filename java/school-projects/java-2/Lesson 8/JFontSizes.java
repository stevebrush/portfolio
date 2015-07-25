/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 25.
 * Chapter 16, Exercise # 1.
 * Description:
 * Create a JFrame that displays a phrase in every font size from 6 through 20.
 */
import javax.swing.*;
import java.awt.*;
public class JFontSizes extends JFrame
{
    private Font myFont = new Font("Serif", Font.ITALIC, 6);
    private String myString = new String("Hello, World!");

    public void paint(Graphics g)
    {
        super.paint(g);
        int increase = 0;

        // Increase the font size for every iteration.
        for (int i = 6; i < 21; ++i)
        {
            g.setFont(myFont.deriveFont((float) i));
            g.drawString(myString, 10 * i, increase);
            increase += g.getFontMetrics().getHeight() + 10;
            System.out.println(increase);
        }
    }

    public static void main(String[] args)
    {
        // Create and display the frame.
        JFontSizes frame = new JFontSizes();
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setSize(500, 500);
        frame.setVisible(true);
    }
}
