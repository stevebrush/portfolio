/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 14.
 * Chapter 15, Exercise # 4.
 * Description:
 * Create a JFrame that uses BorderLayout. Place a JButton in the center region.
 * Each time the user clicks the button, change the background color in one of
 * the other regions.
 */
import java.awt.*;
import java.awt.Color;
import javax.swing.*;
import java.awt.event.*;
import java.util.Random;
public class JColorFrame extends JFrame implements ActionListener
{
    private JButton button = new JButton("Change Color");
    private BorderLayout layout = new BorderLayout(5, 5);
    private JPanel panelNorth = new JPanel();
    private JPanel panelEast = new JPanel();
    private JPanel panelSouth = new JPanel();
    private JPanel panelWest = new JPanel();

    public JColorFrame()
    {
        // Configure frame.
        super("Color Changer");
        setLayout(layout);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(400, 400);

        button.addActionListener(this);

        // Set the panel sizes.
        panelNorth.setPreferredSize(new Dimension(100, 100));
        panelEast.setPreferredSize(new Dimension(100, 100));
        panelSouth.setPreferredSize(new Dimension(100, 100));
        panelWest.setPreferredSize(new Dimension(100, 100));

        // Add panels.
        add(panelNorth, BorderLayout.NORTH);
        add(panelEast, BorderLayout.EAST);
        add(panelSouth, BorderLayout.SOUTH);
        add(panelWest, BorderLayout.WEST);

        // Add button.
        add(button, BorderLayout.CENTER);

        // Show the frame.
        setVisible(true);
    }

    @Override
    /**
     * Set the background of a random panel to a random color.
     */
    public void actionPerformed(ActionEvent e)
    {
        Random rand = new Random();
        float r = rand.nextFloat();
        float g = rand.nextFloat();
        float b = rand.nextFloat();
        Color color = new Color(r, g, b);
        int randomInt = rand.nextInt((3 - 0) + 1);
        switch (randomInt)
        {
            case 0:
            default:
            panelNorth.setBackground(color);
            break;
            case 1:
            panelEast.setBackground(color);
            break;
            case 2:
            panelSouth.setBackground(color);
            break;
            case 3:
            panelWest.setBackground(color);
            break;
        }

    }

    public static void main(String[] args)
    {
        JColorFrame frame = new JColorFrame();
    }
}
