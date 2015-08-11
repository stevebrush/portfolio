/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 28.
 * Final Project: Chapter 15, Game Zone # 2, Page 872 (part A only).
 *
 * Description:
 * ------------
 * Create a game that helps new mouse users improve their hand-eye coordination.
 * Within a JFrame, display an array of 48 JPanels in a GridLayout using eight rows
 * and six columns. Randomly display an X on one of the panels. When the user
 * clicks the correct panel (the one displaying the X), remove the X and display
 * it on a different panel. After the user successfully "hits" the correct panel
 * 10 times, display a congratulatory message that includes the user's percentage
 * (hits divided by clicks).
 *
 * Additional Requirements:
 * ------------------------
 * Use 48 JButtons instead of an array of 48 JPanels.
 * Once the user clicks on the tenth correct button, display your name, course,
 * section number, and MEID by using a JOptionPane message dialog.
 */

import java.awt.*;
import java.awt.event.*;
import javax.swing.*;
import javax.swing.border.*;
import java.util.Random;

public class JCatchTheMouse extends JFrame implements ActionListener
{
    private final String NOT_MOUSE_CHAR = "8";
    private final String MOUSE_CHAR = "X";

    private final int MAX_ROUNDS = 10;
    private final int NUM_BUTTONS = 48;
    private final int DELAY = 1500;

    private int hits = 0;
    private int misses = 0;
    private int round = 0;

    private JPanel wrapper = new JPanel();
    private JButton[] buttons = new JButton[NUM_BUTTONS];
    private JLabel instructions;
    private Timer timer;

    /**
     * Constructor.
     **/
    public JCatchTheMouse()
    {
        // Setup the JFrame...
        super("Catch The Mouse!");
        setSize(250, 300);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        // Game instructions and components.
        instructions = new JLabel("");
        Font myFont = new Font("Courier New", Font.BOLD, 16);
        JPanel game = new JPanel();

        // Padding around edges.
        game.setLayout(new GridLayout(8, 6));
        wrapper.setLayout(new BorderLayout());
        wrapper.setBorder(new EmptyBorder(15, 15, 15, 15));

        // Create each button.
        for (int i = 0; i < NUM_BUTTONS; ++i)
        {
            buttons[i] = new JButton(NOT_MOUSE_CHAR);
            buttons[i].setFont(myFont);
            buttons[i].addActionListener(this);
            buttons[i].setOpaque(true);
            buttons[i].setBorderPainted(false);
            buttons[i].setBackground(Color.WHITE);
            buttons[i].setForeground(Color.BLACK);
            buttons[i].setBorder(BorderFactory.createEmptyBorder(0,0,0,0));
            game.add(buttons[i]);
        }

        // Setup timer.
        timer = new Timer(DELAY, this);
        timer.setInitialDelay(DELAY);
        timer.start();

        // Add elements.
        wrapper.add(instructions, BorderLayout.NORTH);
        wrapper.add(game, BorderLayout.CENTER);
        add(wrapper);
        moveMouse();
        updateInstructions();
        setVisible(true);
    }

    /**
     * Returns a random index to be used with the buttons array.
     */
    public int getRandomPlacement()
    {
        Random random = new Random();
        return random.nextInt(NUM_BUTTONS);
    }

    /**
     * Using the random index, determine which button will be the "mouse".
     */
    public void moveMouse()
    {
        timer.restart();
        int index = getRandomPlacement();
        for (int i = 0; i < NUM_BUTTONS; ++i)
        {
            buttons[i].setText(NOT_MOUSE_CHAR);
            if (i == index)
            {
                buttons[i].setText(MOUSE_CHAR);
                buttons[i].setBackground(Color.WHITE);
                buttons[i].setForeground(Color.BLACK);
            }
        }
    }

    /**
     * Resets the core variables so the player can play again.
     */
    private void resetGame()
    {
        hits = 0;
        misses = 0;
        round = 0;
        moveMouse();
    }

    /**
     * Prints the current round to the user.
     */
    private void updateInstructions()
    {
        String roundStr = "<html><body><div><strong>Click the \"X\" to catch the mouse.</strong><br>Careful! He's a quick little rodent!<hr>Round: " + round + " of " + MAX_ROUNDS + "<br><br></div></body></html>";
        instructions.setText(roundStr);
    }

    /**
     * Event handling.
     */
    @Override
    public void actionPerformed(ActionEvent e)
    {
        // The timer finishes. Move the mouse!
        if (e.getSource() == timer)
        {
            moveMouse();
        }

        // A button was clicked...
        else
        {
            JButton button = (JButton)e.getSource();
            int percentage = 0;
            boolean caught = false;

            // ...the user clicked on the mouse!
            if (button.getText().equals(MOUSE_CHAR))
            {
                ++hits;
                ++round;
                for (int i = 0; i < NUM_BUTTONS; ++i)
                {
                    buttons[i].setBackground(Color.WHITE);
                    buttons[i].setForeground(Color.BLACK);
                }
                caught = true;
            }

            // ...the user clicked on something else. Add a miss to their score.
            else
            {
                button.setBackground(Color.RED);
                button.setForeground(Color.WHITE);
                ++misses;
            }

            // The game is finished. Display the score to the user and reset the game.
            if (round >= MAX_ROUNDS)
            {
                percentage = (int) Math.round(hits / (double) (misses + hits) * 100);
                JOptionPane.showMessageDialog(null, "Finished! \nYou caught the mouse " + percentage + "% of the time!\nMisses: " + misses + "\n\nCredits\n---------\nSteve Brush\nCIS263AA - Class # 13704\nMEID: STE2253193");
                resetGame();
            }

            // The game is not finished. Move the mouse.
            else if (caught == true)
            {
                button.setBackground(Color.GREEN);
                button.setForeground(Color.WHITE);
                updateInstructions();
                moveMouse();
            }
        }
    }

    /**
     * Initializer.
     */
    public static void main(String[] args)
    {
        JCatchTheMouse frame = new JCatchTheMouse();
    }
}
