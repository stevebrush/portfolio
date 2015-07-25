/**
 * @author Steve Brush.
 * Lesson 8, Excercise # 8.
 * CIS163AA
 * Class # 21432
 * 2015 May 6
 * The ConvertDate class takes a date string entered by the user (mm/dd/yyyy)
 * and converts it to a readable version. The class also checks to make sure the
 * date is valid, accounting for leap years.
 */
import java.text.*;
import java.util.*;
import javax.swing.*;
public class ConvertDate
{

    private static JOptionPane pane;
    private static final String ERROR_STRING = "ERROR";
    private static final String[] MONTHS = {"January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"};
    private static final int[] MONTH_DAYS = {31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31};

    public static void main(String[] args)
    {
        boolean isValidDate = false;
        String input;
        String formattedDate = "";

        pane = new JOptionPane();

        // Keep asking for the user to enter a date until it is valid.
        // Or, the user can simply close the dialog.
        while (!isValidDate)
        {
            input = "" + pane.showInputDialog(null, "Enter a date (mm/dd/yyyy):");
            if (input.equals("null")) {
                break;
            } else {
                input = input.trim();
                formattedDate = formatDate(input);
                isValidDate = (formattedDate != ERROR_STRING);
            }
        }

        // Display the results to the user.
        if (isValidDate) {
            pane.showMessageDialog(null, "Congratulations! The date was valid.\n" + formattedDate);
        }
    }

    /**
     * Validates the given string against various restrictions.
     * Note: it would be simpler to use the Calendar class for most of these
     * calculations, but for the purpose of the exercise, we will write them out
     * directly.
     */
    public static String formatDate(String str)
    {
        String[] dateElements = str.split("/");
        int numElements = dateElements.length;
        int[] dateNumbers = new int[numElements];

        StringBuilder formattedDate = new StringBuilder();

        int dayMax;
        int M = 0;
        int D = 1;
        int Y = 2;

        // Check if string contains only two slashes.
        if (dateElements.length != 3)
        {
            pane.showMessageDialog(null, "The address was formatted incorrectly.\nPlease include only two (2) forward slashes (/).");
            return ERROR_STRING;
        }

        // Make sure each element is an integer.
        for (int i = 0; i < numElements; ++i)
        {
            try
            {
                dateNumbers[i] = Integer.parseInt(dateElements[i]);
            }
            catch (NumberFormatException e)
            {
                pane.showMessageDialog(null, "One of the date elements was not a number.\nPlease enter only numbers, in this format: mm/dd/yyyy.");
                return ERROR_STRING;
            }
        }

        // Is the year number at least 4 characters long (1000 - 9999)?
        if (dateNumbers[Y] < 1000 || dateNumbers[Y] > 9999)
        {
            pane.showMessageDialog(null, "The year must be four (4) characters long.");
            return ERROR_STRING;
        }

        // Is the month number valid?
        if (dateNumbers[M] < 1 || dateNumbers[M] > 12)
        {
            pane.showMessageDialog(null, "Please enter a valid month between one (1) and twelve (12).");
            return ERROR_STRING;
        }

        /**
         * February, leap year?
         * A better way to do this would be to check against the Calendar class's
         * getActualMaximum(Calendar.DAY_OF_MONTH) method.
         */
        dayMax = MONTH_DAYS[(dateNumbers[M] - 1)];
        if (dateNumbers[M] == 2) // Feb.
        {
            if (isLeapYear(dateNumbers[Y]))
            {
                dayMax = 29;
            }
        }

        // Check against the day number entered by the user.
        if (dateNumbers[D] < 1 || dateNumbers[D] > dayMax)
        {
            pane.showMessageDialog(null, "Please enter a valid day number.\nThe day you entered (" + dateNumbers[D] + ") does not exist in that month/year.");
            return ERROR_STRING;
        }

        // Format the string into something more readable.
        try
        {
            GregorianCalendar calendar = new GregorianCalendar();

            // Set the date of the Calendar to the date entered by the user.
            calendar.set(Calendar.MONTH, dateNumbers[M] - 1);
            calendar.set(Calendar.DAY_OF_MONTH, dateNumbers[D]);
            calendar.set(Calendar.YEAR, dateNumbers[Y]);

            int dayOfYear = calendar.get(Calendar.DAY_OF_YEAR);

            formattedDate.append("" + MONTHS[dateNumbers[M] - 1]);
            formattedDate.append(" " + dateNumbers[D]);
            formattedDate.append(", " + dateNumbers[Y]);
            formattedDate.append("\nFun Fact: This is the " + dayOfYear + getOrdinalSuffix(dayOfYear) + " day of the year!");

            return formattedDate.toString();
        }
        catch (Exception e)
        {
            pane.showMessageDialog(null, "The date you entered (" + str + ") is not valid.");
        }

        return ERROR_STRING;
    }

    /**
     * Returns 'true' if the year supplied is a leap year.
     */
    private static boolean isLeapYear(int year)
    {
        boolean isDivisibleBy4 = (year % 4 == 0);
        boolean isDivisibleBy100 = (year % 100 == 0);
        boolean isDivisibleBy400 = (year % 400 == 0);
        return (isDivisibleBy400 || (isDivisibleBy4 && !isDivisibleBy100));
    }

    /**
     * Returns an ordinal suffix depending on the integer.
     */
    private static String getOrdinalSuffix(int num)
    {
        // Some numbers don't follow the 'standard' rules.
        if (num >= 11 && num <= 13)
        {
            return "th";
        }
        switch (num % 10)
        {
            case 1:
            return "st";
            case 2:
            return "nd";
            case 3:
            return "rd";
            default:
            return "th";
        }
    }

}
