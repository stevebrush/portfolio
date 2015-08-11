/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 28.
 * Final Project: Chapter 14, Game Zone # 2, Page 799.
 *
 * Description:
 * ------------
 * Create a lottery game. In this game, generate six random numbers, each between zero and 30.
 * Allow the user to choose six check boxes to play the game.
 * (Do not allow the user to select more than six boxes.)
 * After the player has chosen six numbers, display the randomly selected numbers,
 * the player's numbers, and the amount of money the user has won, as follows:
 * 3 matches   = $100
 * 4 matches   = $10,000
 * 5 matches   = $50,000
 * 6 matches   = $1,000,000
 * 0-3 matches = $0
 *
 * Additional Requirements:
 * ------------------------
 * Add a menu bar to the program with a File menu.
 * In the File menu, add a submenu (JMenuItem) called About.
 * When the user clicks on the About menu item, display a JOptionPane message
 * dialog that contains your name, your course, the section number, and MEID.
 */
import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
import javax.swing.border.*;
import java.util.Random;
public class JLottery2 extends JFrame implements ActionListener
{
    private int rangeMax = 30;
    private JCheckBox[] checkboxes = new JCheckBox[rangeMax];
    private JButton button = new JButton("View Results!");
    private JLabel winningNumbers = new JLabel("");
    private JPanel wrapper = new JPanel();
    private JMenuBar mainBar = new JMenuBar();
    private JMenu menu1 = new JMenu("File");
    private JMenuItem link1 = new JMenuItem("About");

    /**
     * Constructor.
     */
    public JLottery2()
    {
        super("Lucky 30 Lottery");
        setSize(570, 300);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setJMenuBar(mainBar);
        mainBar.add(menu1);
        menu1.add(link1);
        link1.addActionListener(this);

        // Wrapper and padding.
        wrapper.setLayout(new BorderLayout());
        wrapper.setBorder(new EmptyBorder(15, 15, 15, 15));

        JLabel instructions = new JLabel("Choose 6 numbers!");
        JPanel panel = new JPanel();

        // Create the checkboxes.
        for (int i = 0; i < rangeMax; ++i)
        {
            checkboxes[i] = new JCheckBox(String.valueOf(i + 1), false);
            checkboxes[i].addActionListener(this);
            panel.add(checkboxes[i]);
        }

        button.addActionListener(this);

        // Add components.
        wrapper.add(instructions, BorderLayout.WEST);
        wrapper.add(panel, BorderLayout.CENTER);
        wrapper.add(button, BorderLayout.SOUTH);
        wrapper.add(winningNumbers, BorderLayout.NORTH);

        add(wrapper);
        setVisible(true);
    }

    /**
     * Checks the selected numbers against the random numbers.
     */
    @Override
    public void actionPerformed(ActionEvent e)
    {
        if (e.getSource() == link1)
        {
            JOptionPane.showMessageDialog(null, "Steve Brush - CIS263AA - Class # 13704 - MEID: STE2253193");
        }
        else
        {
            JCheckBox checkbox;
            int counter = 0;
            int max = 6;
            int i, j = max;
            int numMatches = 0;
            int randomWinner = 0;
            int[] selected = new int[max];
            int[] winners = new int[max];
            Random random = new Random();
            String message = "";
            String winnings = "";
            String selectedNumbers = "";

            // Make sure only 6 checkboxes are selected.
            if (e.getSource() != button)
            {
                for (i = 0; i < rangeMax; ++i)
                {
                    if (checkboxes[i].isSelected())
                    {
                        ++counter;
                    }
                }
                if (counter > max)
                {
                    checkbox = (JCheckBox) e.getSource();
                    checkbox.setSelected(false);
                    JOptionPane.showMessageDialog(null, "Only select " + max + " numbers.");
                }
            }

            // The button was clicked.
            else
            {
                // First, check to make sure that at least 6 numbers were selected.
                for (i = 0; i < rangeMax; ++i)
                {
                    if (checkboxes[i].isSelected())
                    {
                        ++counter;
                    }
                }

                // If not, let the use know.
                if (counter < max)
                {
                    JOptionPane.showMessageDialog(null, "Please select at least " + max + " numbers.");
                }

                // If so...
                else
                {
                    // Generate 6 random numbers.
                    j = max;
                    for (i = 0; i < max; ++i)
                    {
                        // Make sure the random number has not been selected already.
                        randomWinner = 0;
                        while (randomWinner == 0)
                        {
                            randomWinner = random.nextInt(rangeMax) + 1;
                            for (int k = 0; k < winners.length; ++k)
                            {
                                if (winners[k] == randomWinner)
                                {
                                    randomWinner = 0;
                                    break;
                                }
                            }
                        }

                        // Add the winning number.
                        --j;
                        winners[j] = randomWinner;
                    }

                    // Collect the selected numbers.
                    j = max;
                    for (i = 0; i < rangeMax; ++i)
                    {
                        if (checkboxes[i].isSelected())
                        {
                            --j;
                            selected[j] = Integer.parseInt(checkboxes[i].getText());
                            checkboxes[i].setSelected(false);
                            selectedNumbers += selected[j] + " ";
                        }
                    }

                    // Compare!
                    for (i = 0; i < max; ++i)
                    {
                        message += " " + String.valueOf(winners[i]);
                        for (j = 0; j < max; ++j)
                        {
                            if (selected[j] == winners[i])
                            {
                                System.out.println(selected[j]);
                                ++numMatches;
                            }
                        }
                    }

                    // Determine winnings.
                    switch (numMatches)
                    {
                        default:
                        winnings = "$0";
                        break;
                        case 3:
                        winnings = "$100";
                        break;
                        case 4:
                        winnings = "$10,000";
                        break;
                        case 5:
                        winnings = "$50,000";
                        break;
                        case 6:
                        winnings = "$1,000,000";
                        break;
                    }

                    // Display to user.
                    winningNumbers.setText("<html><body>Winning Numbers:  " + message + "<br>Your Numbers:  " + selectedNumbers + "<br>Matches:  " + numMatches + "<br><strong>Winnings:  " + winnings + "</strong></body></html>");

                }
            }
        }
    }

    /**
     * Initialize.
     */
    public static void main(String[] args)
    {
        JLottery2 frame = new JLottery2();
    }
}
