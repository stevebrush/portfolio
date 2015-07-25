/**
 * @author Steve Brush.
 * Lesson 9, Excercise # 6.
 * CIS163AA
 * Class # 21432
 * 2015 May 9
 * The Schedule class displays the class schedules for an entered class name.
 */
import javax.swing.JOptionPane;
public class Schedule
{
    public static void main(String[] args)
    {
        String[][] classInfoTable = {
            {"CS 101", "Wed 9:15 a.m."},
            {"SC 202", "Fri 8:00 a.m."},
            {"EN 401", "Mon 1:30 p.m."},
            {"SP 502", "Thu 10:00 a.m."}
        };
        JOptionPane pane = new JOptionPane();
        String input = "";
        String[] classTable = new String[2];
        boolean entryFound = false;

        // Display possible values in the console.
        System.out.println("*** Possible values:");
        for (int j = 0; j < classInfoTable.length; ++j)
        {
            System.out.println(classInfoTable[j][0] + " | " + classInfoTable[j][1]);
        }

        // Ask the user to provide a class name.
        while (!entryFound)
        {
            entryFound = false;
            input = "" + pane.showInputDialog(null, "Please enter a class name:");
            input = input.trim();
            if (input.equals("null"))
            {
                break;
            }
            else
            {
                // Allow lower case entries.
                input = input.toUpperCase();

                // Loop through all class arrays to see if there's a match.
                outerLoop:
                for (int i = 0; i < classInfoTable.length; ++i)
                {
                    for (int k = 0; k < classInfoTable[i].length; ++k)
                    {
                        // Was the user input found in the array?
                        if (classInfoTable[i][k].equals(input))
                        {
                            // Yes!
                            // Store the found class information to be used later.
                            classTable = classInfoTable[i];
                            entryFound = true;
                            break outerLoop;
                        }
                    }
                }
            }
            // Display the error message.
            if (!entryFound)
            {
                pane.showMessageDialog(null, "The class name you entered was not found.\nYou entered: " + input);
            }
        }
        // Display the successful results to the user.
        if (entryFound)
        {
            pane.showMessageDialog(null, "The class " + classTable[0] + " meets at " + classTable[1]);
        }
    }
}
