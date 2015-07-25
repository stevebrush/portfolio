/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * CIS263AA - Java Programming: Level II - Class # 13704
 * Date: 2015 July 15.
 * Chapter 15, Exercise # 2.
 * Description:
 * Create an educational program for children that distinguishes between vowels
 * and consonants as the user clicks buttons. Create 26 JButtons, each labeled
 * with a different letter of the alphabet. Create a JFrame to hold three JPanels
 * in a two-by-two grid. Randomly select eight of the 26 JButtons and place four
 * in each of the first two JPanels. Add a JLabel to the third JPanel. When the
 * user clicks a JButton, the text of the JLabel identifies the button's letter
 * as a vowel or consonant, and then a new randomly selected letter replaces the
 * letter on the JButton.
 */
import java.awt.*;
import javax.swing.*;
import java.awt.event.*;
import java.util.*;
public class JVowelConsonant extends JFrame implements ActionListener
{
    // Buttons.
    private char[] letters = {
        'A', 'B', 'C', 'D', 'E', 'F',
        'G', 'H', 'I', 'J', 'K', 'L',
        'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X',
        'Y', 'Z'
    };
    private char[] vowels = {
        'A', 'E', 'I', 'O', 'U'
    };
    private ArrayList<Integer> activeIndexes = new ArrayList<Integer>();
    private JButton[] letterButtons = new JButton[26];
    private JLabel label = new JLabel();

    // Panels.
    private JPanel panel1 = new JPanel(new GridLayout(2, 2));
    private JPanel panel2 = new JPanel(new GridLayout(2, 2));
    private JPanel panelSummary = new JPanel(new GridLayout(2, 2));

    public JVowelConsonant()
    {
        // Configure frame.
        super("Vowels and Consonants");
        setLayout(new BorderLayout());
        setSize(600, 200);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        // Instantiate buttons.
        for (int i = 0; i < letters.length; ++i)
        {
            letterButtons[i] = new JButton(Character.toString(letters[i]));
            letterButtons[i].addActionListener(this);
        }

        // Add panels.
        panel1.setPreferredSize(new Dimension(200, 200));
        panel2.setPreferredSize(new Dimension(200, 200));
        panelSummary.setPreferredSize(new Dimension(200, 200));
        panelSummary.add(label);
        add(panel1, BorderLayout.WEST);
        add(panel2, BorderLayout.CENTER);
        add(panelSummary, BorderLayout.EAST);

        // Add buttons and paint the panels.
        generateRandomIndexes(8, 0);
        paintPanels();

        // Display JFrame.
        setVisible(true);
    }

    /**
     * Returns true or false if the letter is a vowel.
     */
    private boolean isVowel(String letter)
    {
        char character = letter.charAt(0);
        boolean found = false;
        for (int i = 0; i < vowels.length; ++i)
        {
            if (vowels[i] == character)
            {
                found = true;
                break;
            }
        }
        return found;
    }

    /**
     * Generates a random integer within a range.
     */
    private int getRandomInt(int min, int max)
    {
        Random random = new Random();
        return random.nextInt((max - min) + 1) + min;
    }

    /**
     * Generates and stores a quantity of random indexes, to be representated
     * against the letters array. These indexes will be used to choose which
     * letter buttons will display to the user.
     */
    private int[] generateRandomIndexes(int quantity, int startIndex)
    {
        int randomInt = 0;
        int[] randomInts = new int[quantity];
        int i = 0;
        int k = 0;
        int min = 0;
        int max = letters.length - 1;

        while (i < quantity)
        {
            randomInt = getRandomInt(min, max);

            // Check if random integer has been selected already.
            if (!activeIndexes.contains(randomInt))
            {
                randomInts[i] = randomInt;
                activeIndexes.add(startIndex, randomInt);
                ++startIndex;
                ++i;
            }
        }

        return randomInts;
    }

    /**
     * Removes buttons and repaints the panels based on the activeIndexes list.
     */
    private void paintPanels()
    {
        panel1.removeAll();
        panel2.removeAll();

        int length = activeIndexes.size();
        int half = length / 2;
        for (int i = 0; i < length; ++i)
        {
            if (i < half)
            {
                panel1.add(letterButtons[activeIndexes.get(i)]);
            }
            else
            {
                panel2.add(letterButtons[activeIndexes.get(i)]);
            }
        }

        panel1.revalidate();
        panel1.repaint();
        panel2.revalidate();
        panel2.repaint();
    }

    @Override
    /**
     * Determine if the letter clicked is a vowel/consonant.
     */
    public void actionPerformed(ActionEvent e)
    {
        Object source = e.getSource();
        JButton button = (JButton) source;
        String letter = button.getText();
        int index = 0;

        // It's a vowel.
        if (isVowel(letter))
        {
            label.setText("The letter '" + letter + "' is a vowel!");
        }

        // It's a consonant.
        else
        {
            label.setText("The letter '" + letter + "' is a consonant.");
        }

        // Look for the letter in the letters array and save the index.
        for (int i = 0; i < letters.length; ++i)
        {
            if (letters[i] == letter.charAt(0))
            {
                index = activeIndexes.indexOf(i);
                activeIndexes.remove(index);
                break;
            }
        }

        // Remove the button and repaint the panels.
        generateRandomIndexes(1, index);
        paintPanels();
    }

    /**
     * Init.
     */
    public static void main(String[] args)
    {
        JVowelConsonant frame = new JVowelConsonant();
    }
}
