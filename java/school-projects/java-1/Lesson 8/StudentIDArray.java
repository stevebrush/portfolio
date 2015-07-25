/**
 * @author Steve Brush.
 * Lesson 8, Excercise # 10.
 * CIS163AA
 * Class # 21432
 * 2015 May 6
 * The StudentIDArray returns student data based on an entered ID.
 */
import javax.swing.*;
public class StudentIDArray
{
    private static JOptionPane pane;
    private static final int ERROR_RESPONSE = -1;

    private static int[] ids = {3523, 1231, 6932, 5345, 2048, 1934, 3812, 4321, 2347, 3492};
    private static float[] averages = {3.2F, 3.5F, 1.2F, 2.3F, 3.3F, 3.8F, 2.9F, 3.8F, 4.0F, 3.2F};
    private static String[] names = {"Sally", "Edwardo", "James", "Dalia", "Sean", "Margot", "Phillip", "Christie", "Devin", "La'Qisha"};

    public static void main(String[] args)
    {
        pane = new JOptionPane();
        boolean isValidId = false;
        String input;
        int index = ERROR_RESPONSE;

        // Keep asking for the user to enter an ID until it is valid.
        // Or, the user can simply close the dialog.
        while (!isValidId)
        {
            input = "" + pane.showInputDialog(null, "Enter a student ID:");
            if (input.equals("null")) {
                break;
            } else {
                input = input.trim();
                index = getStudentIndex(input);
                isValidId = (index != ERROR_RESPONSE);
            }
        }

        // Display the results to the user.
        if (isValidId) {
            pane.showMessageDialog(null, "Student Information:\nStudent ID: " + ids[index] + "\nFirst Name: " + names[index] + "\nGPA: " + averages[index]);
        }

    }

    /**
     * Returns the valid index that can be used to collect information from
     * all student information arrays. If the chosen ID does not exist, this
     * method will return -1.
     */
    private static int getStudentIndex(String studentId)
    {
        int id;
        int index = ERROR_RESPONSE;

        // Is the student ID a number?
        try
        {
            id = Integer.parseInt(studentId);
        }
        catch (NumberFormatException e)
        {
            pane.showMessageDialog(null, "The ID you entered was not a number.\nPlease enter only numbers.");
            return ERROR_RESPONSE;
        }

        // Does the student ID exist in our records?
        for (int i = 0; i < ids.length; ++i)
        {
            if (ids[i] == id)
            {
                index = i;
                break;
            }
        }

        // Tell the user what happened.
        if (index == ERROR_RESPONSE)
        {
            pane.showMessageDialog(null, "The ID you entered, '" + id + "', was not found in our records.\nPlease enter a valid ID:\n" + arrayJoin(ids, ", "));
        }

        return index;

    }

    /**
     * Returns a string consisting of the elements of an array, separated by a
     * chosen character.
     */
    private static String arrayJoin(int[] arr, String delimiter)
    {
        StringBuilder combined = new StringBuilder();
        int length = arr.length;
        for (int i = 0; i < length; ++i)
        {
            combined.append(arr[i]);
            if (i < length - 1)
            {
                combined.append(delimiter);
            }
        }
        return combined.toString();
    }
}
