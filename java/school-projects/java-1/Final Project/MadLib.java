/**
 * @author Steve Brush.
 * MEID: STE2253193.
 * Class: CIS163AA.
 * Section: 21432.
 * Date: 2015 May 25.
 * Final Project, Chapter 2, Game Zone # 1.
 * The MadLib class receives a number of words from the user and replaces key words in a nursery rhyme.
 */
import javax.swing.JOptionPane;
public class MadLib
{
    /**
     * Additional Requirements:
     * ------------------------
     * 1) Use the JOptionPane class to acquire a minimum of six words from
     *    the user to include in the Mad Lib.
     * 2) Ask the user if they would like to play the game again with a
     *    Confirm Dialog box using the JOptionPane.YES_NO_OPTION. If
     *    yes, ask the user for new words and redisplay the new Mad Lib
     *    using them.
     */
    public static void main(String[] args)
    {
        int length = 7;
        int response = 0;
        String input = "";
        String[] words = new String[length];
        String[] word_types = {
            "verb (simple present tense)",
            "adjective",
            "noun (singular)",
            "verb (simple present tense)",
            "noun (singular)",
            "noun (singular)",
            "noun (singular)"
        };

        JOptionPane pane = new JOptionPane();
        boolean quit = false;
        boolean validInput = false;

        outerLoop:
        while (!quit)
        {
            for (int i = 0; i < length; ++i)
            {
                validInput = false;

                // Make sure the input is valid.
                while (!validInput)
                {
                    input = "" + pane.showInputDialog(null, "Please enter a " + word_types[i] + ":");
                    input = input.trim();

                    // User closed the dialog box.
                    if (input.equals("null"))
                    {
                        quit = true;
                        break outerLoop;
                    }
                    else if (input.equals(""))
                    {
                        pane.showMessageDialog(null, "Invalid entry, please type a word!");
                    }
                    else
                    {
                        validInput = true;
                    }
                }

                // Success! Let's add the input to the words array.
                words[i] = input;
            }

            // Show the mad lib to the user.
            if (!quit)
            {
                pane.showMessageDialog(null, words[0].substring(0, 1).toUpperCase() + words[0].substring(1) + ", " + words[0] + ", " + words[1] + " " + words[2] + ",\nHow I " + words[3] + " at what you are.\nUp above the " + words[4] + " so high,\nLike a " + words[5] + " in the " + words[6] + "!");
                response = pane.showConfirmDialog(null, "Play again?", "Play again", JOptionPane.YES_NO_OPTION);
                quit = (response == pane.NO_OPTION);
            }
        }
    }
}
