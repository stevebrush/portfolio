/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 14.
 * Chapter 15, Exercise # 1.
 * Description:
 * Create a JFrame and set the layout to BorderLayout. Place a JButton in each region,
 * and place the name of an appropriate United States landmark on each JButton.
 * For example, New York's Statue of Liberty might be the landmark in the east region.
 */
import java.awt.*;
import javax.swing.*;
public class JLandmarkFrame extends JFrame
{
    public JLandmarkFrame()
    {
        // Configure JFrame.
        super("Landmarks");
        setSize(400, 400);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        // Get the content pane.
        Container container = getContentPane();
        container.setLayout(new BorderLayout());

        // Create the buttons.
        JButton northButton = new JButton("Sears Tower");
        JButton eastButton = new JButton("Statue of Liberty");
        JButton southButton = new JButton("Alamo");
        JButton westButton = new JButton("Big Sur");
        JButton centerButton = new JButton("The Arch");

        // Add the buttons to the JFrame.
        container.add(northButton, BorderLayout.NORTH);
        container.add(eastButton, BorderLayout.EAST);
        container.add(southButton, BorderLayout.SOUTH);
        container.add(westButton, BorderLayout.WEST);
        container.add(centerButton, BorderLayout.CENTER);

        // Display the JFrame.
        setVisible(true);
    }

    /**
     * Init.
     */
    public static void main(String[] args)
    {
        JLandmarkFrame frame = new JLandmarkFrame();
    }
}
