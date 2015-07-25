/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 25.
 * Chapter 16, Exercise # 7.
 * Description:
 * Create a JFrame that displays a yellow smiley face on the screen.
 * When the user clicks the button, make the smiley face frown.
 */
import javax.swing.*;
import java.awt.*;
import java.awt.Color;
import java.awt.event.*;
public class JSmileFace2 extends JFrame implements ActionListener
{
    /**
     * This class handles the painting of the smiley face JPanel.
     */
    public class JGraphicsPanel extends JPanel
    {
        public void paintComponent(Graphics g)
        {
            super.paintComponent(g);
            g.setColor(Color.YELLOW);
            g.fillOval(50, 50, 400, 400);
            g.setColor(Color.WHITE);
            g.fillOval(120, 124, 50, 50);
            g.fillOval(360, 124, 50, 50);
            g.setColor(Color.BLACK);
            g.fillOval(120, 120, 40, 40);
            g.fillOval(380, 120, 40, 40);
            g.drawArc(130, 150, 240, 240, 0, 180 * inverse);
        }
    }

    private JGraphicsPanel panel = new JGraphicsPanel();
    private JButton button = new JButton("Click Me!");
    public int inverse = -1;
    public JSmileFace2()
    {
        // Create the JFrame and button.
        super("Smiley");
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(500, 500);
        setLayout(new BorderLayout());
        button.addActionListener(this);
        add(button, BorderLayout.SOUTH);
        add(panel, BorderLayout.CENTER);
        setVisible(true);
    }

     @Override
    /**
     * Reverse the smiley's mouth and repaint the screen.
     */
    public void actionPerformed(ActionEvent e)
    {
        inverse *= -1;
        panel.repaint();
    }

    public static void main(String[] args)
    {
        JSmileFace2 frame = new JSmileFace2();
    }

}
