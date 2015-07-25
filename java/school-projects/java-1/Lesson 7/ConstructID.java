/**
 * @author Steve Brush.
 * Lesson 7, Excercise # 11.
 * CIS163AA
 * Class # 21432
 * 2015 May 5
 * The ConstructID class creates an ID string from the first letters of a user's
 * full name and the street number from their address.
 */
import javax.swing.*;
public class ConstructID
{
    public static void main(String[] args)
    {
        JOptionPane pane = new JOptionPane();
        String[] names = new String[20];
        String input;
        String address;
        int namesLength;
        int streetNumber = 0;
        boolean isValidName = false;
        boolean isValidAddress = false;
        StringBuilder id;

        // Collect the user's full name.
        while (!isValidName)
        {
            // Get user input.
            input = pane.showInputDialog(null, "Please enter your first and last name:");

            // Strip the trailing white-space.
            input = input.trim();

            // Check the number of words.
            names = input.split(" ");
            namesLength = names.length;
            if (namesLength < 2)
            {
                pane.showMessageDialog(null, "Please enter at least two (2) names.");
            }
            else if (namesLength > 3)
            {
                pane.showMessageDialog(null, "Please enter no more than (3) names.");
            }
            else
            {
                isValidName = true;
            }
        }

        // Get the user's street address.
        while (!isValidAddress)
        {
            address = pane.showInputDialog(null, "Please enter your street address:");
            address = address.trim();

            // A valid street address must include at least one space.
            if (address.indexOf(" ") > -1) {

                // Get the street number from the address string.
                streetNumber = extractStreetNumber(address);

                // Make sure the street number is a valid integer.
                isValidAddress = (streetNumber > -1);
            }

            // Errors were found.
            if (!isValidAddress)
            {
                pane.showMessageDialog(null, "Please enter a valid address, starting with a number, followed by the street name.");
            }
        }

        // Construct the ID.
        id = createAcronym(names);
        id.append(streetNumber);

        // Display it to the user.
        pane.showMessageDialog(null, "Your ID is: " + id.toString());

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

    /**
     * Returns the street number from a street address string.
     * If the first part of the street address is not an integer, return -1.
     **/
    public static int extractStreetNumber(String str)
    {
        try
        {
            String[] parts = str.split(" ");
            return Integer.parseInt(parts[0]);
        }
        catch (NumberFormatException e)
        {
            return -1;
        }
    }
}
