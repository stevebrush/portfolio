/**
 * @author Steve Brush.
 * Lesson 7, Excercise # 8.
 * CIS163AA
 * Class # 21432
 * 2015 May 5
 * The ThreeLetterAcronym displays an acronym based on three words provided by
 * the user.
 */
import javax.swing.JOptionPane;
public class ThreeLetterAcronym
{
    public static void main(String[] args)
    {
        JOptionPane pane = new JOptionPane();
        String input;
        String message;
        String[] words = new String[50];
        StringBuilder acronym;
        boolean hasError = false;
        int wordsLength = 0;

        // Ask the user to provide three words.
        do
        {
            // The user didn't enter three words.
            if (hasError)
            {
                pane.showMessageDialog(null, "You did not enter three words.\nYou entered (" + wordsLength + ") word(s).");
            }

            // Get user input.
            input = pane.showInputDialog(null, "Please enter three words:");

            // Strip the trailing white-space.
            input = input.trim();

            // Check the number of words.
            words = input.split(" ");
            wordsLength = words.length;

            // Set this to true so that the loop will run again.
            hasError = true;
        }
        while (wordsLength != 3);

        // Create an acronym from the input.
        acronym = createAcronym(words);

        // Prepare the message string.
        message = "The original phrase: " + input + "\n";
        message += "The acronym: " + acronym.toString();

        // Display the acronym to the user.
        pane.showMessageDialog(null, message);

    }

    /**
     * Returns a three-letter StringBuilder representing the first letter of
     * each word provided in an array.
     */
    public static StringBuilder createAcronym(String[] words)
    {
        StringBuilder acronym = new StringBuilder();
        int wordsLength = words.length;
        for (int i = 0; i < wordsLength; ++i)
        {
            // Grab the first letter and make it uppercase.
            acronym.append(words[i].toUpperCase().charAt(0));
        }
        return acronym;
    }
}
