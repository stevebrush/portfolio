/**
 * @author Steve Brush.
 * Lesson 9, Excercise # 8.
 * CIS163AA
 * Class # 21432
 * 2015 May 9
 * The DayOfWeek class displays the business hours for an entered day of the week.
 */
import javax.swing.JOptionPane;
public class DayOfWeek
{
    private enum Day {
        SUN, MON, TUE, WED, THU, FRI, SAT
    };
    private static String[] businessHours = {
        "11a - 5p ", "9a - 9p", "9a - 9p", "9a - 9p", "9a - 9p", "9a - 9p", "10a - 6p"
    };
    public static void main(String[] args)
    {
        /**
         * 1. Display a list of the days.
         * 2. Ask the user for a day.
         * 3. Display business hours for a given day.
        */
        String dayString = "";
        JOptionPane pane = new JOptionPane();
        boolean validEntry = false;
        String input = "";
        Day chosenDay = Day.MON;

        // Collect the possible day values into a single string and
        // Display it to the user.
        for (Day day : Day.values())
        {
            dayString += day + " ";
        }
        pane.showMessageDialog(null, "Possible days are:\n" + dayString);

        // Ask the user to provide a day.
        while (!validEntry)
        {
            input = "" + pane.showInputDialog(null, "Enter a day of the week:");
            input  = input.trim();
            if (input.equals("null"))
            {
                break;
            }
            else
            {
                // Allow lower case inputs.
                input = input.toUpperCase();

                // Check against the enum values to make sure the input is valid.
                for (Day day : Day.values())
                {
                    if (input.equals(day.toString()))
                    {
                        chosenDay = day;
                        validEntry = true;
                        break;
                    }
                }
            }

            // The entered day was not valid.
            if (!validEntry)
            {
                pane.showMessageDialog(null, "That is not a valid day. Please try again.");
            }
        }

        // The day was valid, so show the business hours.
        if (validEntry)
        {
            pane.showMessageDialog(null, "Business hours for " + input + ": " + businessHours[chosenDay.ordinal()]);
        }
    }
}
