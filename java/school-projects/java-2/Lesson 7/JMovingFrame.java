/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 14.
 * Chapter 15, Exercise # 5.
 * Description:
 * Create a JFrame with JPanels, a JButton, and a JLabel. When the user clicks the
 * JButton, reposition the JLabel to a new location in a different JPanel.
 */
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.util.*;
public class JMovingFrame extends JFrame implements ActionListener
{
    private JPanel panelNorth = new JPanel();
    private JPanel panelEast = new JPanel();
    private JPanel panelSouth = new JPanel();
    private JPanel panelWest = new JPanel();
    private JLabel label = new JLabel("Move me by clicking the button.");
    private JButton button = new JButton("Click Me!");

    private final int NUM_PANELS = 4;

    /**
     * Constructor.
     */
    public JMovingFrame()
    {
        // Configure the frame.
        super("Move the Label");
        setSize(500, 500);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLayout(new BorderLayout());

        // Configure the panels.
        panelNorth.setPreferredSize(new Dimension(200, 200));
        panelEast.setPreferredSize(new Dimension(200, 200));
        panelSouth.setPreferredSize(new Dimension(200, 200));
        panelWest.setPreferredSize(new Dimension(200, 200));
        panelNorth.add(label);
        button.addActionListener(this);

        // Add the panels.
        add(panelNorth, BorderLayout.NORTH);
        add(panelEast, BorderLayout.EAST);
        add(panelSouth, BorderLayout.SOUTH);
        add(panelWest, BorderLayout.WEST);
        add(button, BorderLayout.CENTER);

        // Show the frame.
        setVisible(true);
    }

    /**
     * Generates a random integer within a range.
     */
    private int getRandomInt(int min, int max)
    {
        Random random = new Random();
        return random.nextInt((max - min) + 1) + min;
    }

    @Override
    /**
     * Moves the label to a random panel on the frame.
     */
    public void actionPerformed(ActionEvent e)
    {
        Random random = new Random();
        int randomInt = getRandomInt(1, NUM_PANELS);
        switch (randomInt)
        {
            case 1:
            panelNorth.add(label);
            break;
            case 2:
            panelEast.add(label);
            break;
            case 3:
            panelSouth.add(label);
            break;
            case 4:
            panelWest.add(label);
            default:
            break;
        }
        panelNorth.revalidate();
        panelNorth.repaint();
        panelEast.revalidate();
        panelEast.repaint();
        panelSouth.revalidate();
        panelSouth.repaint();
        panelWest.revalidate();
        panelWest.repaint();
    }

    /**
     * Init.
     */
    public static void main(String[] args)
    {
        JMovingFrame frame = new JMovingFrame();
    }
}
